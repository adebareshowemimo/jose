# UI Components Inventory

## Current (active workspace)

### Layout-level components (Blade includes)
- `Layout::parts.header` (referenced in `modules/Layout/app.blade.php`)
- `Layout::parts.footer` (referenced in `modules/Layout/app.blade.php`)
- `Layout::parts.login-register-modal` (referenced in `modules/Layout/app.blade.php`)

### Public page structure components (currently embedded)
- Hero search block (in `resources/views/home.blade.php`)
- Process flow strip (in `resources/views/home.blade.php`)
- Job carousel cards (in `resources/views/home.blade.php`)
- Training cards (in `resources/views/home.blade.php`)
- Pipeline tracker (in `resources/views/home.blade.php`)
- Testimonial cards (in `resources/views/home.blade.php`)
- CTA banner block (in `resources/views/home.blade.php`)

## Planned shared component library (Phase 1.8)
Create under `resources/views/components/`:
- `button.blade.php`
- `card.blade.php`
- `badge.blade.php`
- `form-input.blade.php`
- `form-select.blade.php`
- `modal.blade.php`
- `table.blade.php`
- `pagination.blade.php`
- `breadcrumbs.blade.php`
- `alert.blade.php`
- `empty-state.blade.php`

## Notes
- No active `resources/views/components` directory exists yet in current branch.
- Existing reusable components are still mostly inline and should be extracted incrementally during Phase 1.
