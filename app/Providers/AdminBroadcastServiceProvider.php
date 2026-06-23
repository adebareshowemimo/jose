<?php

namespace App\Providers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Company;
use App\Models\ContactSubmission;
use App\Models\EventRegistration;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Payment;
use App\Models\RecruitmentRequest;
use App\Models\TrainingEnrolment;
use App\Support\AdminNotifier;
use Illuminate\Support\ServiceProvider;

/**
 * Hooks the `created` event of every model that feeds an admin badge and pushes a
 * live notification. Centralising the hooks here (instead of editing each controller
 * store() method) guarantees every creation path is covered — including paid event
 * registrations / training enrolments that are created during order fulfilment.
 */
class AdminBroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        JobListing::created(function (JobListing $job) {
            // Contract-staffing roles are admin-posted and move no sidebar badge;
            // only regular employer-posted jobs raise the Jobs signal.
            if (! $job->is_contract_staffing) {
                AdminNotifier::broadcast('jobs', $job);
            }
        });

        JobApplication::created(function (JobApplication $application) {
            $isContract = (bool) $application->jobListing?->is_contract_staffing;
            AdminNotifier::broadcast($isContract ? 'contractStaffing' : 'applications', $application);
        });

        Company::created(fn (Company $company) => AdminNotifier::broadcast('companies', $company));
        RecruitmentRequest::created(fn (RecruitmentRequest $request) => AdminNotifier::broadcast('recruitment', $request));
        ContactSubmission::created(fn (ContactSubmission $contact) => AdminNotifier::broadcast('contacts', $contact));
        Order::created(fn (Order $order) => AdminNotifier::broadcast('orders', $order));
        Payment::created(fn (Payment $payment) => AdminNotifier::broadcast('payments', $payment));
        EventRegistration::created(fn (EventRegistration $registration) => AdminNotifier::broadcast('events', $registration));
        TrainingEnrolment::created(fn (TrainingEnrolment $enrolment) => AdminNotifier::broadcast('training', $enrolment));
        NewsletterSubscriber::created(fn (NewsletterSubscriber $subscriber) => AdminNotifier::broadcast('newsletter', $subscriber));

        ChatMessage::created(function (ChatMessage $message) {
            // Mirror the sidebar 'chat' badge: only count messages addressed to the
            // admin (a non-admin sender inside an admin conversation).
            if ($message->sender_role === ChatMessage::ROLE_ADMIN) {
                return;
            }

            if (in_array($message->conversation?->type, [
                ChatConversation::TYPE_ADMIN_CANDIDATE,
                ChatConversation::TYPE_ADMIN_EMPLOYER,
            ], true)) {
                AdminNotifier::broadcast('chat', $message);
            }
        });
    }
}
