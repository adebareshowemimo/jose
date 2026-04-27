# JOSEOCEANJOBS — Complete Frontend Pages List

## Architecture

| Decision | Choice |
|----------|--------|
| CSS | Tailwind CSS v4 via Vite |
| JS | Alpine.js + Lucide Icons |
| Build | Vite v8 |
| Views | Blade templates in `modules/{Module}/Views/` |
| Layouts | `Layout::` — shared layouts (app, auth, dashboard) |
| Design | JCL Brand: Navy #073057, Teal #1AAD94, Inter font |

---

## 7 Phases, ~60 Pages

### Phase 1: Foundation (Layout Shell + Design System CSS)

Shared infrastructure all pages depend on.

| # | Task | Key Files |
|---|------|-----------|
| 1.1 | Tailwind CSS + Vite config | `vite.config.js`, `resources/css/app.css` |
| 1.2 | CSS design tokens (custom properties) | `resources/css/app.css` `@theme` block |
| 1.3 | Public master layout | `modules/Layout/app.blade.php` |
| 1.4 | Global header/navigation | `modules/Layout/parts/header.blade.php` |
| 1.5 | Global footer | `modules/Layout/parts/footer.blade.php` |
| 1.6 | Auth layout | `modules/Layout/auth/app.blade.php` |
| 1.7 | Dashboard layout + sidebar | `modules/Layout/user.blade.php` + `parts/sidebar.blade.php` |
| 1.8 | Shared Blade components | `resources/views/components/*.blade.php` (buttons, cards, badges, forms, modals, tables, pagination, breadcrumbs, alerts) |

---

### Phase 2: Public Pages (13 pages — highest impact, SEO-critical)

| # | Page | Route | Controller | HTML Ref |
|---|------|-------|------------|----------|
| 2.1 | Homepage | `/` | `HomeController@index` | index.html (22 variants) |
| 2.2 | Job Search/Listing | `/job` | `JobController@index` | job-list-v1 to v17 |
| 2.3 | Job Detail | `/job/{slug}` | `JobController@detail` | job-single.html (7 variants) |
| 2.4 | Job by Category | `/job/category/{slug}` | `JobController@categoryIndex` | job-list variants |
| 2.5 | Candidate Directory | `/candidate` | `CandidateController@index` | candidates-list-v1 to v7 |
| 2.6 | Candidate Profile | `/candidate/{slug}` | `CandidateController@detail` | candidates-single-v1 to v5 |
| 2.7 | Company Directory | `/companies` | `CompanyController@index` | employers-list-v1 to v4 |
| 2.8 | Company Profile | `/companies/{slug}` | `CompanyController@detail` | employers-single-v1 to v3 |
| 2.9 | News/Blog Listing | `/news` | `NewsController@index` | blog-list-v1 to v3 |
| 2.10 | News Detail | `/news/{slug}` | `NewsController@detail` | blog-single.html |
| 2.11 | Contact | `/contact` | `ContactController@index` | contact.html |
| 2.12 | Dynamic CMS Pages | `/{slug}` | `PageController@detail` | about.html, terms.html, faqs.html |
| 2.13 | Plans/Pricing | `/plan` | `PlanController@index` | pricing.html |

**Priority order:** Homepage → Job Search → Job Detail → Contact → Candidates → Companies → News

---

### Phase 3: Auth Pages (5 pages)

| # | Page | HTML Ref |
|---|------|----------|
| 3.1 | Login | login.html |
| 3.2 | Register (Candidate/Employer tabs) | register.html |
| 3.3 | Forgot Password | — |
| 3.4 | Reset Password | — |
| 3.5 | Email Verification | — |

---

### Phase 4: Candidate Dashboard (13 pages)

| # | Page | Route | HTML Ref |
|---|------|-------|----------|
| 4.1 | Dashboard Home | `/user/dashboard` | candidate-dashboard.html |
| 4.2 | My Profile | `/user/candidate/profile` | candidate-dashboard-profile.html |
| 4.3 | Applied Jobs | `/user/applied-jobs` | candidate-dashboard-applied-job.html |
| 4.4 | CV Manager | `/user/cv-manager` | candidate-dashboard-cv-manager.html |
| 4.5 | Job Alerts | `/user/job-alerts` | candidate-dashboard-job-alerts.html |
| 4.6 | Bookmarks | `/user/bookmark` | candidate-dashboard-shortlisted-resume.html |
| 4.7 | Resume Builder | — | candidate-dashboard-resume.html |
| 4.8 | Change Password | `/user/profile/change-password` | dashboard-change-password.html |
| 4.9 | Messages/Chat | `/user/chat` | dashboard-messages.html |
| 4.10 | Notifications | `/user/notifications` | — |
| 4.11 | Plans & Billing | `/user/my-plan` | dashboard-packages.html |
| 4.12 | Wallet | `/user/wallet` | — |
| 4.13 | Payout | `/user/payout` | — |

---

### Phase 5: Employer Dashboard (10 pages)

| # | Page | Route | HTML Ref |
|---|------|-------|----------|
| 5.1 | Dashboard Home | `/user/dashboard` (role-switched) | dashboard.html |
| 5.2 | Company Profile | `/user/company/profile` | dashboard-company-profile.html |
| 5.3 | Post New Job | `/user/new-job` | dashboard-post-job.html |
| 5.4 | Manage Jobs | `/user/manage-jobs` | dashboard-manage-job.html |
| 5.5 | Edit Job | `/user/edit-job/{id}` | dashboard-post-job.html |
| 5.6 | Applicants | `/user/applicants` | dashboard-applicants.html |
| 5.7 | Browse Resumes | — | dashboard-resumes.html |
| 5.8 | Resume Alerts | — | dashboard-resume-alerts.html |
| 5.9 | Messages | `/user/chat` | (shared with 4.9) |
| 5.10 | Change Password | — | (shared with 4.8) |

---

### Phase 6: Transactional Pages (7 pages)

| # | Page | HTML Ref |
|---|------|----------|
| 6.1 | Checkout | shop-checkout.html |
| 6.2 | Cart | shopping-cart.html |
| 6.3 | Order Detail | — |
| 6.4 | Order History | — |
| 6.5 | Booking Checkout | — |
| 6.6 | Invoice | invoice.html |
| 6.7 | Order Complete | order-completed.html |

---

### Phase 7: Error Pages (4 pages)

| # | Page |
|---|------|
| 7.1 | 404 Not Found |
| 7.2 | 500 Server Error |
| 7.3 | 403 Forbidden |
| 7.4 | Maintenance |
