# Extractable Components (Layout-first)

These are high-value components to extract first for Superdesign `<sd-component>` reuse.

## Priority 1 — Global layout components

1. **Header/Nav**
   - Source candidates:
     - `resources/views/layouts/app.blade.php` (inline nav block)
     - `modules/Layout/parts/header.blade.php` (target canonical partial)
   - Suggested props:
     - `activeNav` (string)
     - `isAuthenticated` (boolean)
     - `loginHref`, `registerHref`, `jobsHref`, `trainingHref` (strings)

2. **Footer**
   - Source candidates:
     - `resources/views/layouts/app.blade.php` (inline footer block)
     - `modules/Layout/parts/footer.blade.php` (target canonical partial)
   - Suggested props:
     - `newsletterAction` (string)
     - `year` (string/number)

3. **Dashboard Sidebar**
   - Source candidate:
     - `modules/Layout/parts/sidebar.blade.php` (to create/restore)
   - Suggested props:
     - `activeItem` (string)
     - `role` (candidate|employer)

## Priority 2 — Reusable cards
4. Job card
5. Candidate card
6. Company card
7. Stat card
8. Empty state

## Readiness status
- Header/Footer exist inline in current layout and can be extracted immediately.
- Sidebar/user layout exists in backup and should be restored before extraction.
