<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Frontend\AuthPageController;
use App\Http\Controllers\Frontend\PublicPageController;
use App\Http\Controllers\Frontend\CandidateDashboardController;
use App\Http\Controllers\Frontend\EmployerDashboardController;
use App\Http\Controllers\Frontend\TransactionPageController;
use App\Http\Controllers\Frontend\ErrorPageController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\CVManagerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\CompleteSignupController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\EmailTemplateController as AdminEmailTemplateController;
use App\Http\Controllers\Admin\ApplicationNotificationController;
use App\Http\Controllers\Admin\RecruitmentRequestController as AdminRecruitmentRequestController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\NewsArticleController as AdminNewsArticleController;
use App\Http\Controllers\Admin\ContactSubmissionController as AdminContactSubmissionController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\JobTypeController as AdminJobTypeController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Frontend\RecruitmentRequestController;
use App\Http\Controllers\Frontend\ContactSubmissionController;
use App\Http\Controllers\Frontend\ChatController;
use App\Http\Controllers\Frontend\NotificationController;

// Public pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PublicPageController::class, 'about'])->name('about.index');
Route::get('/leadership', [PublicPageController::class, 'leadership'])->name('leadership.index');
Route::get('/partnerships', [PublicPageController::class, 'partnerships'])->name('partnerships.index');
Route::get('/training', [PublicPageController::class, 'training'])->name('training.index');

// Services (Training becomes a sub-service; old /training URL preserved for compat)
Route::get('/services', [PublicPageController::class, 'services'])->name('services.index');
Route::get('/services/training', [PublicPageController::class, 'servicesTraining'])->name('services.training');
Route::get('/services/training/soft-skills', [PublicPageController::class, 'servicesTrainingSoft'])->name('services.training.soft');
Route::get('/services/training/technical', [PublicPageController::class, 'servicesTrainingTechnical'])->name('services.training.technical');
Route::get('/services/crew-management', [PublicPageController::class, 'servicesCrewManagement'])->name('services.crew-management');
Route::get('/services/ship-chandelling', [PublicPageController::class, 'servicesShipChandelling'])->name('services.ship-chandelling');
Route::get('/services/crew-abandonment', [PublicPageController::class, 'servicesCrewAbandonment'])->name('services.crew-abandonment');
Route::get('/services/marine-procurement', [PublicPageController::class, 'servicesMarineProcurement'])->name('services.marine-procurement');
Route::get('/services/marine-insurance', [PublicPageController::class, 'servicesMarineInsurance'])->name('services.marine-insurance');
Route::get('/services/travel-management', [PublicPageController::class, 'servicesTravelManagement'])->name('services.travel-management');

// Career
Route::get('/career', [PublicPageController::class, 'career'])->name('career.index');
Route::get('/career/apprenticeship', [PublicPageController::class, 'careerApprenticeship'])->name('career.apprenticeship');
Route::get('/career/internship', [PublicPageController::class, 'careerInternship'])->name('career.internship');

// Events
Route::get('/events', [PublicPageController::class, 'events'])->name('events.index');
Route::get('/job', [PublicPageController::class, 'jobsIndex'])->name('job.index');
Route::get('/job/{slug}', [PublicPageController::class, 'jobDetail'])->name('job.detail');
Route::get('/job/category/{slug}', [PublicPageController::class, 'jobCategory'])->name('job.category');
Route::get('/candidate', [PublicPageController::class, 'candidatesIndex'])->name('candidate.index');
Route::get('/candidate/{slug}', [PublicPageController::class, 'candidateDetail'])->name('candidate.detail');
Route::get('/companies', [PublicPageController::class, 'companiesIndex'])->name('companies.index');
Route::get('/companies/{slug}', [PublicPageController::class, 'companyDetail'])->name('companies.detail');
Route::get('/news', [PublicPageController::class, 'newsIndex'])->name('news.index');
Route::get('/news/{slug}', [PublicPageController::class, 'newsDetail'])->name('news.detail');

// Newsletter
Route::post('/newsletter/subscribe', [\App\Http\Controllers\Frontend\NewsletterController::class, 'subscribe'])
    ->middleware('throttle:10,1')
    ->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\Frontend\NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\Frontend\NewsletterController::class, 'unsubscribeConfirm'])
    ->name('newsletter.unsubscribe.confirm');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact.index');
Route::post('/contact', [ContactSubmissionController::class, 'store'])->name('contact.store');
Route::get('/contact/thread/{token}', [ContactSubmissionController::class, 'thread'])->name('contact.thread');
Route::post('/contact/thread/{token}', [ContactSubmissionController::class, 'reply'])->name('contact.thread.reply');
Route::get('/plan', [PublicPageController::class, 'plan'])->name('plan.index');

// Auth pages
Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthPageController::class, 'login'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
    Route::get('/register', [AuthPageController::class, 'register'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    // Password reset
    Route::get('/forgot-password', [AuthPageController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])
        ->middleware('throttle:6,1')
        ->name('auth.forgot-password.submit');
    Route::get('/reset-password/{token}', [AuthPageController::class, 'resetPassword'])->name('auth.reset-password');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:6,1')
        ->name('auth.reset-password.submit');

    // Email verification
    Route::get('/verify-email', [VerifyEmailController::class, 'notice'])->middleware('auth')->name('auth.verify-email');
    Route::post('/verify-email/resend', [VerifyEmailController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('auth.verify-email.resend');

    // Social login
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->where('provider', 'google|microsoft')
        ->name('auth.social.redirect');
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->where('provider', 'google|microsoft')
        ->name('auth.social.callback');

    // OAuth role-picker
    Route::get('/finish-signup', [CompleteSignupController::class, 'show'])->middleware('auth')->name('auth.complete-signup');
    Route::post('/finish-signup', [CompleteSignupController::class, 'submit'])->middleware('auth')->name('auth.complete-signup.submit');
});

// Email verification signed link (Laravel default name 'verification.verify')
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

// Candidate dashboard pages
Route::prefix('user')->middleware(['auth', 'role.selected'])->group(function () {
    Route::get('/dashboard', [CandidateDashboardController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/candidate/profile', [ProfileController::class, 'show'])->name('user.candidate.profile');
    
    // Profile update routes
    Route::post('/candidate/profile/basic', [ProfileController::class, 'updateBasicInfo'])->name('user.profile.basic.update');
    Route::post('/candidate/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('user.profile.avatar.update');
    Route::post('/candidate/profile/experience', [ProfileController::class, 'addExperience'])->name('user.profile.experience.add');
    Route::put('/candidate/profile/experience/{id}', [ProfileController::class, 'updateExperience'])->name('user.profile.experience.update');
    Route::delete('/candidate/profile/experience/{id}', [ProfileController::class, 'deleteExperience'])->name('user.profile.experience.delete');
    Route::post('/candidate/profile/education', [ProfileController::class, 'addEducation'])->name('user.profile.education.add');
    Route::put('/candidate/profile/education/{id}', [ProfileController::class, 'updateEducation'])->name('user.profile.education.update');
    Route::delete('/candidate/profile/education/{id}', [ProfileController::class, 'deleteEducation'])->name('user.profile.education.delete');
    Route::post('/candidate/profile/certification', [ProfileController::class, 'addCertification'])->name('user.profile.certification.add');
    Route::put('/candidate/profile/certification/{id}', [ProfileController::class, 'updateCertification'])->name('user.profile.certification.update');
    Route::delete('/candidate/profile/certification/{id}', [ProfileController::class, 'deleteCertification'])->name('user.profile.certification.delete');
    Route::post('/candidate/profile/social', [ProfileController::class, 'updateSocialLinks'])->name('user.profile.social.update');
    Route::post('/candidate/profile/summary', [ProfileController::class, 'updateSummary'])->name('user.profile.summary.update');
    Route::post('/candidate/profile/skills', [ProfileController::class, 'updateSkills'])->name('user.profile.skills.update');
    
    Route::get('/applied-jobs', [CandidateDashboardController::class, 'appliedJobs'])->name('user.applied-jobs');
    
    // CV Manager routes
    Route::get('/cv-manager', [CVManagerController::class, 'index'])->name('user.cv-manager');
    Route::post('/cv-manager/upload', [CVManagerController::class, 'upload'])->name('user.cv.upload');
    Route::get('/cv-manager/{resume}/download', [CVManagerController::class, 'download'])->name('user.cv.download');
    Route::post('/cv-manager/{resume}/default', [CVManagerController::class, 'setDefault'])->name('user.cv.default');
    Route::delete('/cv-manager/{resume}', [CVManagerController::class, 'destroy'])->name('user.cv.delete');
    
    // Job Alerts routes
    Route::get('/job-alerts', [CandidateDashboardController::class, 'jobAlerts'])->name('user.job-alerts');
    Route::post('/job-alerts', [CandidateDashboardController::class, 'storeAlert'])->name('user.alert.store');
    Route::post('/job-alerts/{alert}/toggle', [CandidateDashboardController::class, 'toggleAlert'])->name('user.alert.toggle');
    Route::delete('/job-alerts/{alert}', [CandidateDashboardController::class, 'deleteAlert'])->name('user.alert.delete');
    
    // Bookmarks routes
    Route::get('/bookmark', [CandidateDashboardController::class, 'bookmarks'])->name('user.bookmark');
    Route::post('/bookmark/{job}', [CandidateDashboardController::class, 'toggleBookmark'])->name('user.bookmark.toggle');
    Route::delete('/bookmark/{wishlist}', [CandidateDashboardController::class, 'removeBookmark'])->name('user.bookmark.remove');
    
    Route::get('/resume-builder', [CandidateDashboardController::class, 'resumeBuilder'])->name('user.resume-builder');
    Route::get('/profile/change-password', [CandidateDashboardController::class, 'changePassword'])->name('user.change-password');
    Route::post('/profile/change-password', [CandidateDashboardController::class, 'updatePassword'])->name('user.password.update');
    Route::get('/chat', [ChatController::class, 'candidate'])->name('user.chat');
    Route::post('/chat/{conversation}/messages', [ChatController::class, 'sendCandidateMessage'])->name('user.chat.messages.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('user.notifications');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('user.notifications.read-all');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('user.notifications.read');
    Route::get('/my-plan', [CandidateDashboardController::class, 'plans'])->name('user.my-plan');
    Route::get('/wallet', [CandidateDashboardController::class, 'wallet'])->name('user.wallet');
    Route::get('/payout', [CandidateDashboardController::class, 'payout'])->name('user.payout');
});

// Employer dashboard pages
Route::prefix('employer')->middleware(['auth', 'role.selected'])->group(function () {
    Route::get('/dashboard', [EmployerDashboardController::class, 'dashboard'])->name('employer.dashboard');
    Route::get('/company/profile', [EmployerDashboardController::class, 'companyProfile'])->name('employer.company.profile');
    Route::post('/company/profile', [EmployerDashboardController::class, 'updateCompanyProfile'])->name('employer.company.profile.update');
    Route::post('/company/logo', [EmployerDashboardController::class, 'updateCompanyLogo'])->name('employer.company.logo.update');
    Route::delete('/company/logo', [EmployerDashboardController::class, 'deleteCompanyLogo'])->name('employer.company.logo.delete');
    Route::post('/company/cover', [EmployerDashboardController::class, 'updateCompanyCover'])->name('employer.company.cover.update');
    Route::get('/new-job', [EmployerDashboardController::class, 'postJob'])->name('employer.new-job');
    Route::post('/new-job', [EmployerDashboardController::class, 'storeJob'])->name('employer.new-job.store');
    Route::get('/manage-jobs', [EmployerDashboardController::class, 'manageJobs'])->name('employer.manage-jobs');
    Route::get('/edit-job/{id}', [EmployerDashboardController::class, 'editJob'])->name('employer.edit-job');
    Route::put('/edit-job/{id}', [EmployerDashboardController::class, 'updateJob'])->name('employer.edit-job.update');
    Route::get('/applicants', [EmployerDashboardController::class, 'applicants'])->name('employer.applicants');
    Route::get('/resumes', [EmployerDashboardController::class, 'browseResumes'])->name('employer.resumes');
    Route::get('/resume-alerts', [EmployerDashboardController::class, 'resumeAlerts'])->name('employer.resume-alerts');
    Route::get('/chat', [ChatController::class, 'employer'])->name('employer.chat');
    Route::post('/chat/{conversation}/messages', [ChatController::class, 'sendEmployerMessage'])->name('employer.chat.messages.store');
    Route::post('/chat/{conversation}/schedule-interview', [ChatController::class, 'scheduleInterview'])->name('employer.chat.schedule-interview');
    Route::post('/chat/{conversation}/request-documents', [ChatController::class, 'requestDocuments'])->name('employer.chat.request-documents');
    Route::post('/chat/{conversation}/send-offer', [ChatController::class, 'sendOffer'])->name('employer.chat.send-offer');
    Route::get('/change-password', [EmployerDashboardController::class, 'changePassword'])->name('employer.change-password');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('employer.notifications');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('employer.notifications.read-all');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('employer.notifications.read');

    // Recruitment / Hiring Services
    Route::prefix('recruitment-requests')->name('employer.recruitment-requests.')->group(function () {
        Route::get('/', [RecruitmentRequestController::class, 'index'])->name('index');
        Route::get('/create', [RecruitmentRequestController::class, 'create'])->name('create');
        Route::post('/', [RecruitmentRequestController::class, 'store'])->name('store');
        Route::get('/{recruitment}', [RecruitmentRequestController::class, 'show'])->name('show');
        Route::post('/{recruitment}/cancel', [RecruitmentRequestController::class, 'cancel'])->name('cancel');
        Route::post('/{recruitment}/candidates/{candidate}/decision', [RecruitmentRequestController::class, 'decide'])->name('candidate.decide');
    });
});

// Transactional pages — all require login.
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [TransactionPageController::class, 'checkout'])->name('checkout');
    Route::get('/cart', [TransactionPageController::class, 'cart'])->name('cart');
    Route::get('/order/{id}', [TransactionPageController::class, 'orderDetail'])->name('order.detail');
    Route::post('/order/{order}/pay/paystack', [PaymentController::class, 'paystackInit'])->name('payment.paystack.init');
    Route::post('/order/{order}/pay/manual', [PaymentController::class, 'manualSubmit'])->name('payment.manual.submit');
    Route::get('/user/orders', [TransactionPageController::class, 'orderHistory'])->name('order.history');
    Route::get('/booking/{code}/checkout', [TransactionPageController::class, 'bookingCheckout'])->name('booking.checkout');
    Route::get('/user/booking/{code}/invoice', [TransactionPageController::class, 'invoice'])->name('booking.invoice');
    Route::get('/order-completed', [TransactionPageController::class, 'orderComplete'])->name('order.completed');
});

// Paystack redirect / webhook — public so it survives session expiry during the hosted-page hop.
// Identity is verified via the unguessable reference + Paystack secret-key API call.
Route::get('/payment/paystack/callback', [PaymentController::class, 'paystackCallback'])->name('payment.paystack.callback');

// Error pages
Route::prefix('errors')->group(function () {
    Route::get('/404', [ErrorPageController::class, 'notFound'])->name('errors.404');
    Route::get('/500', [ErrorPageController::class, 'serverError'])->name('errors.500');
    Route::get('/403', [ErrorPageController::class, 'forbidden'])->name('errors.403');
    Route::get('/maintenance', [ErrorPageController::class, 'maintenance'])->name('errors.maintenance');
});

// ─── Admin Panel ─────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.users.reset-password');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Companies
    Route::get('/companies', [AdminController::class, 'companies'])->name('admin.companies');
    Route::get('/companies/{company}', [AdminController::class, 'showCompany'])->name('admin.companies.show');
    Route::put('/companies/{company}', [AdminController::class, 'updateCompany'])->name('admin.companies.update');
    Route::delete('/companies/{company}', [AdminController::class, 'deleteCompany'])->name('admin.companies.delete');

    // Job Listings
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('admin.jobs');
    Route::put('/jobs/{job}', [AdminController::class, 'updateJob'])->name('admin.jobs.update');
    Route::delete('/jobs/{job}', [AdminController::class, 'deleteJob'])->name('admin.jobs.delete');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::get('/job-types', [AdminJobTypeController::class, 'index'])->name('admin.job-types.index');
    Route::post('/job-types', [AdminJobTypeController::class, 'store'])->name('admin.job-types.store');
    Route::put('/job-types/{jobType}', [AdminJobTypeController::class, 'update'])->name('admin.job-types.update');
    Route::delete('/job-types/{jobType}', [AdminJobTypeController::class, 'destroy'])->name('admin.job-types.destroy');
    Route::get('/locations', [AdminLocationController::class, 'index'])->name('admin.locations.index');
    Route::post('/locations', [AdminLocationController::class, 'store'])->name('admin.locations.store');
    Route::put('/locations/{location}', [AdminLocationController::class, 'update'])->name('admin.locations.update');
    Route::delete('/locations/{location}', [AdminLocationController::class, 'destroy'])->name('admin.locations.destroy');

    // Plans
    Route::get('/plans', [AdminController::class, 'plans'])->name('admin.plans');
    Route::post('/plans', [AdminController::class, 'storePlan'])->name('admin.plans.store');
    Route::put('/plans/{plan}', [AdminController::class, 'updatePlan'])->name('admin.plans.update');
    Route::delete('/plans/{plan}', [AdminController::class, 'deletePlan'])->name('admin.plans.delete');

    // Subscriptions
    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('admin.subscriptions');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/orders/{order}/payments/{payment}/verify', [AdminController::class, 'verifyPayment'])->name('admin.orders.payments.verify');
    Route::post('/orders/{order}/payments/{payment}/reject', [AdminController::class, 'rejectPayment'])->name('admin.orders.payments.reject');

    // Payments
    Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');

    // Applications
    Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications');

    // Chat
    Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::get('/chat/candidates/search', [AdminChatController::class, 'searchCandidates'])->name('admin.chat.candidates.search');
    Route::post('/chat/{conversation}/messages', [AdminChatController::class, 'send'])->name('admin.chat.messages.store');

    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');

    // News
    Route::get('/news', [AdminNewsArticleController::class, 'index'])->name('admin.news.index');
    Route::post('/news', [AdminNewsArticleController::class, 'store'])->name('admin.news.store');
    Route::put('/news/{article}', [AdminNewsArticleController::class, 'update'])->name('admin.news.update');
    Route::delete('/news/{article}', [AdminNewsArticleController::class, 'destroy'])->name('admin.news.destroy');

    // Contact submissions
    Route::get('/contacts', [AdminContactSubmissionController::class, 'index'])->name('admin.contacts.index');
    Route::get('/contacts/create', [AdminContactSubmissionController::class, 'create'])->name('admin.contacts.create');
    Route::post('/contacts', [AdminContactSubmissionController::class, 'store'])->name('admin.contacts.store');
    Route::get('/contacts/{contact}', [AdminContactSubmissionController::class, 'show'])->name('admin.contacts.show');
    Route::get('/contacts/{contact}/edit', [AdminContactSubmissionController::class, 'edit'])->name('admin.contacts.edit');
    Route::put('/contacts/{contact}', [AdminContactSubmissionController::class, 'update'])->name('admin.contacts.update');
    Route::delete('/contacts/{contact}', [AdminContactSubmissionController::class, 'destroy'])->name('admin.contacts.destroy');
    Route::post('/contacts/{contact}/respond', [AdminContactSubmissionController::class, 'respond'])->name('admin.contacts.respond');

    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/mail/test', [AdminSettingsController::class, 'testMail'])->name('admin.settings.mail.test');

    // Email Templates
    Route::get('/email-templates', [AdminEmailTemplateController::class, 'index'])->name('admin.email-templates.index');
    Route::get('/email-templates/{template}', [AdminEmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
    Route::put('/email-templates/{template}', [AdminEmailTemplateController::class, 'update'])->name('admin.email-templates.update');
    Route::get('/email-templates/{template}/preview', [AdminEmailTemplateController::class, 'preview'])->name('admin.email-templates.preview');
    Route::post('/email-templates/{template}/send-test', [AdminEmailTemplateController::class, 'sendTest'])->name('admin.email-templates.send-test');

    // Bulk application notifications
    Route::post('/applications/send-notification', [ApplicationNotificationController::class, 'send'])->name('admin.applications.send-notification');

    // Newsletter Subscribers
    Route::prefix('newsletter')->name('admin.newsletter.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'export'])->name('export');
        Route::patch('/{subscriber}/unsubscribe', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'unsubscribe'])->name('unsubscribe');
        Route::patch('/{subscriber}/reactivate', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'reactivate'])->name('reactivate');
        Route::delete('/{subscriber}', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'destroy'])->name('destroy');
    });

    // Social Media Settings
    Route::get('/social', [\App\Http\Controllers\Admin\SocialSettingsController::class, 'index'])->name('admin.social.index');
    Route::put('/social', [\App\Http\Controllers\Admin\SocialSettingsController::class, 'update'])->name('admin.social.update');

    // Recruitment Requests
    Route::prefix('recruitment-requests')->name('admin.recruitment-requests.')->group(function () {
        Route::get('/', [AdminRecruitmentRequestController::class, 'index'])->name('index');
        Route::get('/{recruitment}', [AdminRecruitmentRequestController::class, 'show'])->name('show');
        Route::put('/{recruitment}', [AdminRecruitmentRequestController::class, 'update'])->name('update');
        Route::post('/{recruitment}/quote', [AdminRecruitmentRequestController::class, 'quote'])->name('quote');
        Route::post('/{recruitment}/candidates/platform', [AdminRecruitmentRequestController::class, 'attachCandidate'])->name('attach-candidate');
        Route::post('/{recruitment}/candidates/external', [AdminRecruitmentRequestController::class, 'uploadCv'])->name('upload-cv');
        Route::delete('/{recruitment}/candidates/{candidate}', [AdminRecruitmentRequestController::class, 'removeCandidate'])->name('remove-candidate');
        Route::post('/{recruitment}/notify', [AdminRecruitmentRequestController::class, 'notify'])->name('notify');
    });
});

// Dynamic CMS pages (keep last)
Route::get('/{slug}', [PublicPageController::class, 'cms'])->name('cms.page');
