# JOSEOCEANJOBS — Design System

## Product Context
Maritime job portal connecting seafarers, deck officers, and marine engineers with global shipping companies, offshore operators, and vessel owners. Built for Jose Consulting Ltd. (JCL).

## Brand Identity
- **Brand name**: JOSEOCEANJOBS
- **Company**: Jose Consulting Ltd.
- **Abbreviation logo**: JCL (circular teal badge)
- **Primary tagline (brand-approved)**: "Empowering Skills, Connecting Worlds."
- **Campaign headline (homepage hero only)**: "Your Career at Sea Starts Here"
- **Vertical**: Maritime / Offshore / Marine recruitment

---

## Source-of-Truth & Lock Rules

### Source precedence
1. **`docs/JCL Brand Guide.pdf`** — primary visual source (branding, typography, logo intent)
2. **`docs/JCL JOB CENTER WEBSITE CONTENT.docx`** — approved messaging/tagline/copy direction
3. **`docs/JCL JOB CENTER WEBSITE.docx` + `docs/SOP - JOSE CONSULTING.docx`** — product scope and workflow context

### Lock rules
- Do not introduce new fonts outside this file without explicit approval.
- Keep brand tagline exactly as specified above; homepage campaign headline is allowed as supporting copy only.
- Any new page/component must consume these tokens before custom styles.

---

## Color Palette

| Token | Hex | Usage |
|---|---|---|
| `--color-primary` | `#073057` | Main navy — navbar bg, headings, CTA dark, footer bg |
| `--color-accent` | `#1AAD94` | Teal — CTA buttons, highlights, icons, badges, active states |
| `--color-dark` | `#2C2C2C` | Body text color |
| `--color-light` | `#F5F5F5` | Light section backgrounds |
| `--color-border` | `#E0E0E0` | Card borders, dividers |
| `--color-muted` | `#6B7280` | Secondary/muted text |
| White | `#FFFFFF` | Page background, text on dark bg |
| Success green | `#16A34A` | Salary text (positive) |

---

## Typography

| Role | Font Family | Weights |
|---|---|---|
| Body / UI | Inter | 400, 500, 600, 700 |
| Display / Headings | Inter | 600, 700, 800 |
| Monospace / Data | JetBrains Mono *(utility use only)* | 500 |

**Font source (brand-locked primary)**: Google Fonts (Inter)  
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

**Heading style**: `letter-spacing: -0.02em` on all h1–h4

---

## Spacing & Layout

| Token | Value |
|---|---|
| Container max-width | `container mx-auto` (Tailwind default 1280px) |
| Container padding | `px-6` |
| Section padding | `py-24` |
| Card border-radius | `rounded-[12px]` |
| Button border-radius | `rounded-[6px]` / `rounded-[8px]` |
| Input border-radius | `rounded-[12px]` |

---

## Component Patterns

### Navbar
- Height: `h-[72px]`, `sticky top-0 z-50`
- Background: `bg-[#073057]`, bottom border: `border-white/10`
- Logo: teal circle `w-12 h-12 bg-[#1AAD94] rounded-full` + "JCL" text
- Nav links: `text-white text-[14px] font-medium uppercase tracking-[0.05em] hover:text-[#1AAD94]`
- Login button: `border border-white rounded-[6px]` (outline style)
- Primary CTA: `bg-[#1AAD94] rounded-[6px]` (filled teal)

### Buttons
- **Primary**: `bg-[#1AAD94] text-white font-bold rounded-[8px] uppercase tracking-wider px-8 py-4 hover:brightness-110`
- **Outline dark**: `border-2 border-[#073057] text-[#073057] rounded-[8px] hover:bg-[#073057] hover:text-white`
- **Outline white**: `border border-white text-white rounded-[6px] hover:bg-white/10`

### Cards
- Default: `bg-white border border-[#E0E0E0] rounded-[12px] p-8 hover:border-[#1AAD94] hover:shadow-xl transition-all`
- Featured (dark): `bg-[#073057] border border-[#073057] rounded-[12px] p-8 shadow-xl`

### Badges / Tags
- Accent pill: `bg-[#1AAD94]/10 text-[#1AAD94] text-[10px] px-3 py-1 rounded-full font-bold uppercase`
- Section label: `inline-block px-4 py-1 bg-[#1AAD94]/10 text-[#1AAD94] rounded-full text-[12px] font-bold uppercase tracking-widest mb-4`

### Section Headings
```html
<div class="inline-block px-4 py-1 bg-[#1AAD94]/10 text-[#1AAD94] rounded-full text-[12px] font-bold uppercase tracking-widest mb-4">Label</div>
<h2 class="text-[40px] font-extrabold text-[#073057] leading-tight">Heading Text</h2>
```

---

## Background Patterns

- **Hero dot grid**: `background-image: radial-gradient(rgba(26,173,148,0.15) 1.5px, transparent 1.5px); background-size: 32px 32px;`
- **Dark section grid**: SVG grid pattern with `stroke="white"` at 5% opacity
- **Light section**: `bg-[#F9FAFB]`

---

## Animations

- `compass-spin`: `transform: rotate` 60s linear infinite (decorative compass ring)
- `pipeline-pulse`: scale 1→1.1 + opacity pulse, 2s cubic bezier (active pipeline stage)

---

## Icon Library
- **Iconify** via CDN: `https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js`
- Prefix: `lucide:` and `mdi:` icons
- Usage: `<iconify-icon icon="lucide:ship" class="text-white text-7xl"></iconify-icon>`

---

## Page Structure (Home)
1. Sticky Navbar (`bg-[#073057]`)
2. Split Hero — left: headline + search bar, right: decorative compass (`bg-[#073057]`)
3. Process Flow strip (`bg-[#F5F5F5]`) — Apply → Train → Certify → Match → Deploy
4. Featured Jobs Carousel (`bg-white`)
5. Training Programs Grid (`bg-[#F9FAFB]`) — image cards
6. Career Pipeline Tracker (`bg-[#073057]`) — stage progress visualization
7. Testimonials Grid (`bg-white`) — quote cards with avatars
8. CTA Banner (`bg-white`) — full-width with harbor photo + overlay
9. Footer (`bg-[#073057]`) — 4-column grid + newsletter
