# Candidate Boost — analysis and build plan

Status: proposed, not yet built.
Scope agreed: full build, phased. Packages stored in the database.

---

## 1. What already works

The boost feature is further along than the static page suggests. Worth
knowing before changing anything, because most of it should be left alone.

| Piece | State |
|---|---|
| `candidate_boosts` table | Well formed: `candidate_id`, `order_id`, `days`, `starts_at`, `ends_at`, `status`, `price`, `currency` |
| Purchase flow | `CandidateBoostController::purchase` creates an Order + OrderItem, redirects to payment |
| Payment fulfilment | `CandidateHandler::handlePaid` activates the boost on payment |
| Stacking | Buying while already boosted **extends** from the current `featured_until` rather than overwriting it. Correct, and easy to break — preserve it |
| Actual visibility | `Candidate::scopeFeatured` / `isFeatured` read `featured_until`; search ordering already honours it |
| Activation email | `candidate.boost_activated` is seeded and sends on payment |
| Candidate nav | "Boost Profile" already in the candidate dashboard sidebar |

**The purchase → pay → activate → appear-at-top loop is functional today.**
The gaps are administration, lifecycle and revenue retention.

---

## 2. Gaps found

### 2.1 Prices and durations are effectively hardcoded

`CandidateBoostController::packages()` reads settings keys
(`candidate_boost.price_7d`, `_30d`, `_90d`) but **no such settings rows
exist** — verified: the `candidate_boost` group is empty. So the literal
fallbacks 9 / 29 / 69 are what every visitor sees, and there is no admin
screen that would ever write those keys.

Durations are hardcoded in two places that must agree:

- `packages()` — the `days` values 7 / 30 / 90
- `purchase()` — the validation rule `in:7,30,90`

Changing a duration today means editing both, plus a deploy.

### 2.2 Boosts never expire in the data — the important one

`CandidateBoost::STATUS_EXPIRED` is declared and **never assigned anywhere
in the codebase**. Nothing transitions a row out of `active`.

Note what is *not* broken: actual placement lapses correctly, because
visibility is driven by `featured_until` on the candidate, not by this
column. Users are not affected today.

But every row stays `active` forever, so any admin list, count, filter or
revenue figure built on `status` is wrong the moment it is written.

**This must be fixed before the admin screens are built on top of it,**
otherwise the first thing the new subscribers page shows is bad data.

### 2.3 No admin visibility

No route, controller, view or sidebar entry for boosts. Boost income is
invisible except as untyped rows in the generic orders list.

### 2.4 No lifecycle emails

Only activation exists. Nothing tells a candidate their boost is about to
lapse — which is precisely the moment renewal revenue is won or lost.

---

## 3. Build plan

Ordered so each phase leaves the app working, and so the data bug is fixed
before anything reads it.

### Phase 1 — Packages become data

- `boost_packages` table: `label`, `tagline`, `days`, `price`,
  `is_active`, `sort_order`, `perks` (json)
- `BoostPackage` model, `active()` + ordered scopes
- Migration seeds the current three tiers at today's prices, so behaviour
  is unchanged on deploy
- `CandidateBoostController` reads packages from the table; validation
  changes from `in:7,30,90` to "must be an active package id", removing the
  duplicated duration list
- Admin CRUD at `/admin/boosts/packages` (pattern: `TrainingProgramController`)

**Deliberate:** price is read from the package at purchase time and copied
onto the order. Later admin price edits must never retroactively change a
completed order.

### Phase 2 — Expiry (fixes 2.2)

- `boosts:expire` command: flips `active` rows whose `ends_at` has passed
  to `expired`. Idempotent, `--dry-run`, chunked
- Scheduled daily; `withoutOverlapping()`
- One-off backfill for existing rows
- Does **not** touch `featured_until` — visibility already works and is not
  this command's business

### Phase 3 — Admin subscribers

- `/admin/boosts` — who is boosted: candidate, package, spend, start, end,
  days remaining, status. Filters (active / expiring / expired), search,
  sort, CSV export
- `/admin/boosts/{id}` — detail: timeline, linked order and payment,
  manual extend / cancel / refund-mark, each recorded
- Sidebar entry + dashboard KPIs (active boosts, boost revenue)

### Phase 4 — Lifecycle emails

- Seed `boost.expiring_soon` and `boost.expired` alongside the existing
  templates
- `boosts:send-reminders` command, mirroring `SendCandidateReminders`:
  settings-gated, `--dry-run`, `EmailLog`-deduplicated so a resend or a
  double cron run cannot double-send
- Admin-configurable lead time (default 3 days)
- Admin bell notification on purchase, via the existing `AdminNotifier`
- Purchase receipt only if it should read differently from the activation
  email that already sends

### Phase 5 — Gating

All four senses of "gate", admin-configurable:

**Eligibility** — min profile completeness, CV required, verified email,
block when a boost is already active. Enforced in `purchase()`, and shown
as an explanation on the page rather than a silent failure.

**Feature switch** — master on/off; hides the sidebar entry and blocks the
routes.

**Perks per tier** — top of search, featured badge, homepage spotlight,
employer-search priority. Stored on `boost_packages.perks`. Only meaningful
once the search layer reads them, so ship the flags with the one or two
perks actually wired, not a row of decorative checkboxes.

**Caps** — max stacked days, cooldown, max purchases per year. Enforced in
the same place as eligibility.

---

## 4. Risks

- **Order integrity.** Editing a package price must not alter historical
  orders. Copy price onto the order at purchase; never join back to the
  package for a completed order's amount.
- **Stacking regression.** The extend-from-`featured_until` behaviour is
  subtle and easy to lose while refactoring `packages()`. Needs a test
  pinning it before Phase 1 changes that method.
- **Double-sending reminders.** Mitigated by `EmailLog` dedup, as in the
  existing reminder command.
- **Perks that promise more than they deliver.** A "homepage spotlight"
  toggle that nothing reads is worse than no toggle.
- **Currency.** Package prices are in the site default currency. Follow the
  single-currency rule already established: format via the helper, never
  convert.

---

## 5. Open questions

1. **Refunds.** Should an admin cancelling a boost claw back `featured_until`
   (immediate loss of placement) or let the paid time run out? Recommend the
   latter by default, with an explicit "revoke now" action.
2. **Perks to wire first.** Which of the four are real today? Top-of-search
   already exists via `featured_until`. The other three need work in the
   search and homepage layers, and should be scoped separately.
3. **Reminder cadence.** One email 3 days out, or a second on the final day?
