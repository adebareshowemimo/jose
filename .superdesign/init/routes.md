# Frontend Route Map

## Currently active (`routes/web.php`)
- `GET /` → returns `view('home')`

## Target frontend routes (source: `frontend-pages.md`)

### Public pages
- `/` (Homepage)
- `/job` (Job listing/search)
- `/job/{slug}` (Job detail)
- `/job/category/{slug}` (Job by category)
- `/candidate` (Candidate directory)
- `/candidate/{slug}` (Candidate profile)
- `/companies` (Company directory)
- `/companies/{slug}` (Company profile)
- `/news` (Blog listing)
- `/news/{slug}` (Blog detail)
- `/contact` (Contact)
- `/{slug}` (Dynamic CMS page)
- `/plan` (Pricing/plans)

### Auth
- `/login`
- `/register`
- `/password/reset`
- `/password/reset/{token}`
- `/email/verify`

### Candidate dashboard
- `/user/dashboard`
- `/user/candidate/profile`
- `/user/applied-jobs`
- `/user/cv-manager`
- `/user/job-alerts`
- `/user/bookmark`
- `/user/profile/change-password`
- `/user/chat`
- `/user/notifications`
- `/user/my-plan`
- `/user/wallet`
- `/user/payout`

### Employer dashboard
- `/user/dashboard` (role-switched)
- `/user/company/profile`
- `/user/new-job`
- `/user/manage-jobs`
- `/user/edit-job/{id}`
- `/user/applicants`
- `/user/chat`

### Transactional
- `/checkout`
- `/cart`
- `/order/{id}`
- `/user/order`
- `/booking/{code}/checkout`
- `/user/booking/{code}/invoice`
- `/order-complete`

## Notes
- Current route file is intentionally minimal during redesign.
- Route expansion should follow phase order from `frontend-pages.md`.
