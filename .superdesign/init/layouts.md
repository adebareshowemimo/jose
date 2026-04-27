# Layout Inventory

## Active layouts

### 1) `resources/views/layouts/app.blade.php`
- Tailwind CDN + Fontshare + Iconify based shell
- Contains sticky nav, footer, and `@yield('content')`
- Currently drives `resources/views/home.blade.php`

### 2) `modules/Layout/app.blade.php`
- Vite + Tailwind v4 shell (`@vite(['resources/css/app.css','resources/js/app.js'])`)
- Includes partials:
  - `Layout::parts.header`
  - `Layout::parts.footer`
  - `Layout::parts.login-register-modal`
- Includes flash toast UI and SEO/meta handling

## Planned layouts (from frontend scope)
- Public master: `modules/Layout/app.blade.php`
- Auth layout: `modules/Layout/auth/app.blade.php` (to create/restore)
- Dashboard layout: `modules/Layout/user.blade.php` (to create/restore)
- Dashboard sidebar partial: `modules/Layout/parts/sidebar.blade.php` (to create/restore)

## Recommendation
- Consolidate on `modules/Layout/app.blade.php` + Vite pipeline as canonical layout.
- Migrate page-by-page from legacy `resources/views/layouts/app.blade.php` shell to module layout for consistency.
