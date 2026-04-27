# Page Inventory & Dependency Trees

## Implemented now

### Homepage
- Route: `/`
- View: `resources/views/home.blade.php`
- Layout: `resources/views/layouts/app.blade.php`
- Dependencies:
  - Tailwind CDN + inline custom CSS
  - Iconify CDN
  - Fontshare fonts

## Existing module pages (already present in workspace)
- `modules/Candidate/Views/frontend/index.blade.php`
- `modules/Company/Views/frontend/index.blade.php`
- `modules/Contact/Views/index.blade.php`
- `modules/News/Views/frontend/index.blade.php`

## Planned complete inventory (source: `frontend-pages.md`)
- Phase 1 Foundation
- Phase 2 Public pages (13)
- Phase 3 Auth pages (5)
- Phase 4 Candidate dashboard (13)
- Phase 5 Employer dashboard (10)
- Phase 6 Transactional pages (7)
- Phase 7 Error pages (4)

Total planned: ~60 frontend pages.

## Suggested page dependency baseline (for all new pages)
1. `modules/Layout/app.blade.php` (or auth/user variant)
2. shared header/footer/sidebar partials
3. shared blade components (`resources/views/components/*`) when created
4. `resources/css/app.css`
5. optional page-specific blade partials in each module

## Build order
1. Foundation shell/components
2. Public SEO pages
3. Auth pages
4. Role dashboards
5. Transactions
6. Error pages
