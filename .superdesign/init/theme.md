# Theme & Design Tokens

## Source of truth
- `.superdesign/design-system.md`
- `resources/css/app.css` (`@theme` token block)

## Core brand tokens
- Primary: `#073057`
- Accent: `#1AAD94`
- Dark text: `#2C2C2C`
- Light background: `#F5F5F5`
- Border: `#E0E0E0`
- Muted text: `#6B7280`

## Semantic tokens (from CSS)
- Success: `#16A34A`
- Warning: `#F59E0B`
- Danger: `#DC2626`
- Info: `#2563EB`

## Typography
- Sans: Inter (`--font-sans`)
- Mono: JetBrains Mono (`--font-mono`)
- Also used in legacy layout: General Sans + Satoshi via Fontshare

## UI conventions
- Uppercase nav/button labels with tracking
- Rounded corners (6/8/12px)
- Strong section spacing (`py-24` in homepage shell)
- Maritime visual motif (hero dot-grid, pipeline stages, ship/compass iconography)

## Motion patterns
- `compass-spin`
- `pipeline-pulse`
- `skeleton-pulse`
- `toast-in`

## Build system alignment
- Preferred stack for implementation: Vite + Tailwind v4 + Alpine/Lucide
- Legacy homepage currently still uses Tailwind CDN in `resources/views/layouts/app.blade.php`
