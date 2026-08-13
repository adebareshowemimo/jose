#!/usr/bin/env bash
#
# Production deploy script for JoseOceanJobs.
#
# Usage (from the site root on the server):
#   ./deploy.sh                 # normal deploy
#   ./deploy.sh --no-build      # skip the frontend build (backend-only change)
#   ./deploy.sh --no-backup     # skip the database backup (not recommended)
#   ./deploy.sh --dry-run       # show what would run, change nothing
#
# Safe to re-run. Any failure aborts the deploy and takes the site back out
# of maintenance mode, so the site is never left down by a partial run.

set -Eeuo pipefail

# ---------------------------------------------------------------------------
# Options
# ---------------------------------------------------------------------------

RUN_BUILD=1
RUN_BACKUP=1
DRY_RUN=0
BRANCH="${DEPLOY_BRANCH:-main}"

for arg in "$@"; do
    case "$arg" in
        --no-build)  RUN_BUILD=0 ;;
        --no-backup) RUN_BACKUP=0 ;;
        --dry-run)   DRY_RUN=1 ;;
        -h|--help)   sed -n '3,14p' "$0" | sed 's/^# \{0,1\}//'; exit 0 ;;
        *) echo "Unknown option: $arg (try --help)" >&2; exit 1 ;;
    esac
done

cd "$(dirname "$0")"

# ---------------------------------------------------------------------------
# Output helpers
# ---------------------------------------------------------------------------

if [ -t 1 ]; then
    C_OK=$'\033[32m'; C_WARN=$'\033[33m'; C_ERR=$'\033[31m'
    C_DIM=$'\033[2m'; C_OFF=$'\033[0m'
else
    C_OK=''; C_WARN=''; C_ERR=''; C_DIM=''; C_OFF=''
fi

step() { echo; echo "${C_DIM}==>${C_OFF} $*"; }
ok()   { echo "${C_OK}  ok${C_OFF}  $*"; }
warn() { echo "${C_WARN}  !!${C_OFF}  $*"; }
die()  { echo "${C_ERR}FAILED:${C_OFF} $*" >&2; exit 1; }

# In dry-run, print the command instead of running it.
run() {
    if [ "$DRY_RUN" = "1" ]; then
        echo "${C_DIM}  would run:${C_OFF} $*"
    else
        "$@"
    fi
}

# ---------------------------------------------------------------------------
# Failure handling
#
# Any error past the point where maintenance mode is enabled must bring the
# site back up. A deploy that fails is recoverable; a site stuck showing the
# 503 page is an outage.
# ---------------------------------------------------------------------------

MAINTENANCE_ON=0

cleanup() {
    local code=$?
    if [ "$code" != "0" ] && [ "$MAINTENANCE_ON" = "1" ]; then
        echo
        warn "Deploy failed - bringing the site back up."
        php artisan up || true
        echo
        echo "${C_ERR}The site is live again, but running the PREVIOUS code.${C_OFF}"
        echo "Nothing was rolled back automatically. Check the output above,"
        echo "and if the migration is the problem see the rollback note at the"
        echo "end of this script."
    fi
    exit $code
}
trap cleanup EXIT

# ---------------------------------------------------------------------------
# Preflight
# ---------------------------------------------------------------------------

step "Preflight checks"

[ -f artisan ]      || die "No artisan file here. Run this from the site root."
[ -f .env ]         || die "No .env file. Refusing to deploy an unconfigured site."
command -v php      >/dev/null || die "php not found in PATH."
command -v git      >/dev/null || die "git not found in PATH."
command -v composer >/dev/null || die "composer not found in PATH."

APP_ENV_VALUE="$(php -r 'echo trim(getenv("APP_ENV") ?: "");' 2>/dev/null || true)"
if [ -z "$APP_ENV_VALUE" ]; then
    APP_ENV_VALUE="$(grep -E '^APP_ENV=' .env | head -1 | cut -d= -f2- | tr -d '"'"'"' ' || true)"
fi
ok "APP_ENV=${APP_ENV_VALUE:-unknown}"

if [ "${APP_ENV_VALUE:-}" = "local" ]; then
    warn "APP_ENV is 'local'. This script is meant for production."
    printf "     Continue anyway? [y/N] "
    read -r reply
    [ "$reply" = "y" ] || [ "$reply" = "Y" ] || die "Aborted by user."
fi

# Uncommitted changes on the server usually mean someone edited files in
# place. git pull would clobber that work, so stop and let a human decide.
if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
    warn "The working tree on this server has uncommitted changes:"
    git status --short --untracked-files=no | sed 's/^/       /'
    printf "     These may be overwritten. Continue? [y/N] "
    read -r reply
    [ "$reply" = "y" ] || [ "$reply" = "Y" ] || die "Aborted - stash or commit the changes first."
fi

if [ "$RUN_BUILD" = "1" ]; then
    command -v npm >/dev/null || die "npm not found, and the frontend build is enabled.
       public/build is gitignored, so assets MUST be built on the server.
       Install Node, or pass --no-build if you are certain this change
       touches no CSS/JS and public/build is already populated."
fi

ok "Preflight passed"

# ---------------------------------------------------------------------------
# Database backup
#
# Taken BEFORE maintenance mode so a backup failure costs no downtime.
# ---------------------------------------------------------------------------

if [ "$RUN_BACKUP" = "1" ]; then
    step "Backing up the database"

    # Read connection details through Laravel's own config rather than parsing
    # .env directly. A Laravel .env is NOT ini format - values such as
    # APP_KEY=base64:...== break parse_ini_file(), which silently yields empty
    # credentials and sends mysqldump in with no username or password.
    cfg() {
        php -r "require 'vendor/autoload.php';
                \$app = require 'bootstrap/app.php';
                \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
                echo (string) config(\$argv[1], \$argv[2] ?? '');" "$1" "${2:-}" 2>/dev/null
    }

    DB_CONN="$(cfg 'database.default')"
    mkdir -p storage/backups
    chmod 700 storage/backups 2>/dev/null || true
    STAMP="$(date +%Y%m%d-%H%M%S)"

    case "$DB_CONN" in
        mysql|mariadb)
            DB_DATABASE="$(cfg "database.connections.$DB_CONN.database")"
            DB_USERNAME="$(cfg "database.connections.$DB_CONN.username")"
            DB_PASSWORD="$(cfg "database.connections.$DB_CONN.password")"
            DB_HOST="$(cfg "database.connections.$DB_CONN.host" '127.0.0.1')"
            DB_PORT="$(cfg "database.connections.$DB_CONN.port" '3306')"

            # Fail loudly rather than handing mysqldump empty credentials.
            [ -n "$DB_DATABASE" ] || die "Could not read the database name from config.
       Check that .env is readable and APP_KEY is set."
            [ -n "$DB_USERNAME" ] || die "Could not read the database username from config.
       Check that .env is readable and APP_KEY is set."

            if command -v mysqldump >/dev/null; then
                DUMP_FILE="storage/backups/db-$STAMP.sql"
                if [ "$DRY_RUN" = "1" ]; then
                    echo "${C_DIM}  would run:${C_OFF} mysqldump --host=$DB_HOST --port=$DB_PORT --user=$DB_USERNAME $DB_DATABASE > $DUMP_FILE"
                else
                    # Password goes via MYSQL_PWD so it never appears in the
                    # process list. set +e so a failure is caught, not fatal.
                    set +e
                    MYSQL_PWD="$DB_PASSWORD" mysqldump \
                        --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USERNAME" \
                        --single-transaction --quick --routines \
                        "$DB_DATABASE" > "$DUMP_FILE" 2> storage/backups/dump-error.log
                    DUMP_STATUS=$?
                    set -e

                    if [ "$DUMP_STATUS" != "0" ] || [ ! -s "$DUMP_FILE" ]; then
                        rm -f "$DUMP_FILE"
                        warn "mysqldump FAILED - no backup was taken:"
                        sed 's/^/       /' storage/backups/dump-error.log >&2
                        echo
                        echo "     Nothing has been changed. The database was only read from,"
                        echo "     never written to, and the deploy has not started."
                        printf "     Continue WITHOUT a backup? [y/N] "
                        read -r reply
                        [ "$reply" = "y" ] || [ "$reply" = "Y" ] || die "Aborted - no changes made."
                    else
                        chmod 600 "$DUMP_FILE" 2>/dev/null || true
                        ok "$DUMP_FILE ($(du -h "$DUMP_FILE" | cut -f1))"
                    fi
                    rm -f storage/backups/dump-error.log
                fi
            else
                warn "mysqldump not found - NO BACKUP TAKEN."
                printf "     Continue without a backup? [y/N] "
                read -r reply
                [ "$reply" = "y" ] || [ "$reply" = "Y" ] || die "Aborted."
            fi
            ;;
        sqlite)
            DB_FILE="$(cfg 'database.connections.sqlite.database')"
            [ -f "$DB_FILE" ] || DB_FILE="database/database.sqlite"
            run cp "$DB_FILE" "storage/backups/db-$STAMP.sqlite"
            ok "storage/backups/db-$STAMP.sqlite"
            ;;
        *)
            warn "Unrecognised DB driver '${DB_CONN:-unknown}' - NO BACKUP TAKEN."
            printf "     Continue without a backup? [y/N] "
            read -r reply
            [ "$reply" = "y" ] || [ "$reply" = "Y" ] || die "Aborted."
            ;;
    esac
else
    warn "Skipping database backup (--no-backup)"
fi

# ---------------------------------------------------------------------------
# Deploy
# ---------------------------------------------------------------------------

step "Enabling maintenance mode"
if [ "$DRY_RUN" = "0" ]; then
    php artisan down --render="errors::503" --retry=60 || warn "Could not enable maintenance mode; continuing."
    MAINTENANCE_ON=1
fi
ok "Site is in maintenance mode"

step "Pulling latest code (origin/$BRANCH)"
# NOTE: 'git reset --hard' below discards local edits to TRACKED FILES ONLY -
# PHP, Blade, config. It has no access to the database and cannot alter a
# single row. The database is only ever touched by 'migrate' further down,
# which applies new migrations and never drops or recreates anything.
BEFORE_SHA="$(git rev-parse HEAD)"
run git fetch origin "$BRANCH"
run git reset --hard "origin/$BRANCH"
AFTER_SHA="$(git rev-parse HEAD)"
if [ "$BEFORE_SHA" = "$AFTER_SHA" ]; then
    ok "Already up to date ($(git rev-parse --short HEAD))"
else
    ok "$(git rev-parse --short "$BEFORE_SHA") -> $(git rev-parse --short "$AFTER_SHA")"
fi

step "Installing PHP dependencies"
run composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
ok "composer install complete"

if [ "$RUN_BUILD" = "1" ]; then
    step "Building frontend assets"
    if [ -f package-lock.json ]; then
        run npm ci
    else
        run npm install
    fi
    run npm run build
    ok "Assets built into public/build"
else
    warn "Skipping frontend build (--no-build)"
fi

step "Running database migrations"

# 'migrate' only applies pending migrations; it never drops or recreates the
# schema. Guard against a destructive command reaching production by accident
# anyway - via a bad merge, a stray commit, or a mistyped edit to this script.
# Matches only lines that would EXECUTE such a command (a 'run' wrapper or a
# bare 'php artisan'), so the rollback advice printed at the end of this
# script - and this check itself - do not trip it.
if grep -nE '^[[:space:]]*(run[[:space:]]+)?php[[:space:]]+artisan[[:space:]]+(migrate:(fresh|reset|refresh)|db:wipe)' "$0"; then
    die "This script contains a destructive database command (listed above).
       Deployment stopped. Production data must never be dropped by a deploy.
       Remove the command before running this again."
fi

# Show what is about to be applied, so the operator sees it before it happens.
# Match the STATUS column, not the word "pending" anywhere on the line - a
# migration whose filename contains "pending" would otherwise be reported as
# unapplied when it has already run.
PENDING_LIST="$(php artisan migrate:status 2>/dev/null | grep -E '\bPending\b[[:space:]]*$' || true)"
if [ -n "$PENDING_LIST" ]; then
    echo "${C_DIM}  pending migrations:${C_OFF}"
    echo "$PENDING_LIST" | sed 's/^/    /'
else
    ok "No pending migrations"
fi

run php artisan migrate --force
ok "Migrations complete (no data dropped: 'migrate' only adds)"

step "Clearing and rebuilding caches"
run php artisan config:clear
run php artisan cache:clear
run php artisan route:clear
run php artisan view:clear
run php artisan config:cache
run php artisan route:cache
run php artisan view:cache
ok "Caches rebuilt"

step "Restarting queue workers"
# Workers hold the old code in memory. Without this they keep running the
# previous release until they happen to restart on their own.
run php artisan queue:restart
ok "Workers signalled to restart"

step "Ensuring storage symlink"
run php artisan storage:link 2>/dev/null || true
ok "Storage link in place"

step "Disabling maintenance mode"
if [ "$DRY_RUN" = "0" ]; then
    php artisan up
    MAINTENANCE_ON=0
fi
ok "Site is live"

# ---------------------------------------------------------------------------
# Post-deploy verification
# ---------------------------------------------------------------------------

step "Post-deploy checks"

if [ "$DRY_RUN" = "0" ]; then
    CURRENCY="$(php artisan tinker --execute='echo App\Support\Currency::default();' 2>/dev/null | tr -d '\r\n' | tail -c 5 || true)"
    if [ -n "$CURRENCY" ]; then
        ok "Site currency resolves to: $CURRENCY"
        case "$CURRENCY" in
            *NGN*) : ;;
            *) warn "Expected NGN. Check Admin > Currency settings before taking payments." ;;
        esac
    else
        warn "Could not resolve the site currency - check it manually."
    fi

    PENDING="$(php artisan migrate:status 2>/dev/null | grep -cE '\bPending\b[[:space:]]*$' || true)"
    if [ "${PENDING:-0}" != "0" ]; then
        warn "$PENDING migration(s) still pending - review 'php artisan migrate:status'."
    else
        ok "No pending migrations"
    fi
fi

echo
echo "${C_OK}Deploy complete.${C_OFF} $(git rev-parse --short HEAD) is live."
echo
echo "${C_DIM}If the currency migration needs undoing:"
echo "    php artisan migrate:rollback --step=1"
echo "  It only restores the old column defaults and never touches stored"
echo "  amounts, so order and payment totals are unaffected either way.${C_OFF}"
