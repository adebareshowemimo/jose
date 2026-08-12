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

    DB_CONN="$(php artisan tinker --execute='echo DB::connection()->getDriverName();' 2>/dev/null | tr -d '\r\n' | tail -c 20 || true)"
    mkdir -p storage/backups
    STAMP="$(date +%Y%m%d-%H%M%S)"

    case "$DB_CONN" in
        mysql|mariadb)
            DB_DATABASE="$(php -r '$e=parse_ini_file(".env"); echo $e["DB_DATABASE"]??"";')"
            DB_USERNAME="$(php -r '$e=parse_ini_file(".env"); echo $e["DB_USERNAME"]??"";')"
            DB_PASSWORD="$(php -r '$e=parse_ini_file(".env"); echo $e["DB_PASSWORD"]??"";')"
            DB_HOST="$(php -r '$e=parse_ini_file(".env"); echo $e["DB_HOST"]??"127.0.0.1";')"
            if command -v mysqldump >/dev/null; then
                run bash -c "MYSQL_PWD='$DB_PASSWORD' mysqldump \
                    --host='$DB_HOST' --user='$DB_USERNAME' \
                    --single-transaction --quick --routines \
                    '$DB_DATABASE' > 'storage/backups/db-$STAMP.sql'"
                ok "storage/backups/db-$STAMP.sql"
            else
                warn "mysqldump not found - NO BACKUP TAKEN."
                printf "     Continue without a backup? [y/N] "
                read -r reply
                [ "$reply" = "y" ] || [ "$reply" = "Y" ] || die "Aborted."
            fi
            ;;
        sqlite)
            DB_FILE="$(php -r '$e=parse_ini_file(".env"); echo $e["DB_DATABASE"]??"database/database.sqlite";')"
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
run php artisan migrate --force
ok "Migrations complete"

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

    PENDING="$(php artisan migrate:status 2>/dev/null | grep -c "Pending" || true)"
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
