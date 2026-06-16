<?php

namespace App\Support;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Company;
use App\Models\ContactSubmission;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Payment;
use App\Models\RecruitmentRequest;
use Illuminate\Support\Facades\Schema;

/**
 * Live "needs attention" feed for admins, derived from existing data — there are
 * no stored notification rows. Each item disappears automatically once the admin
 * handles the underlying record (approves the job, confirms the payment, etc.).
 *
 * Categories: jobs pending approval, payments awaiting confirmation, new hiring
 * (recruitment) requests, and new contact messages.
 */
class AdminAlerts
{
    /**
     * Lightweight counts + deep-links for the topbar bell.
     *
     * @return array<int, array{key:string,label:string,count:int,url:string}>
     */
    public static function categories(): array
    {
        $out = [];

        if (Schema::hasTable('job_listings')) {
            $out[] = [
                'key' => 'jobs',
                'label' => 'Jobs pending approval',
                'count' => JobListing::where('status', 'pending')->count(),
                'url' => route('admin.jobs', ['status' => 'pending']),
            ];
        }

        if (Schema::hasTable('payments')) {
            $out[] = [
                'key' => 'payments',
                'label' => 'Payments awaiting confirmation',
                'count' => Payment::where('status', 'pending')->count(),
                'url' => route('admin.payments', ['status' => 'pending']),
            ];
        }

        if (Schema::hasTable('recruitment_requests')) {
            $out[] = [
                'key' => 'recruitment',
                'label' => 'New hiring requests',
                'count' => RecruitmentRequest::where('status', 'pending')->count(),
                'url' => route('admin.recruitment-requests.index', ['status' => 'pending']),
            ];
        }

        if (Schema::hasTable('contact_submissions')) {
            $out[] = [
                'key' => 'contacts',
                'label' => 'New contact messages',
                'count' => ContactSubmission::where('status', 'new')->count(),
                'url' => route('admin.contacts.index', ['status' => 'new']),
            ];
        }

        return $out;
    }

    /**
     * Total number of items needing attention (the badge number).
     */
    public static function total(): int
    {
        return array_sum(array_column(self::categories(), 'count'));
    }

    /**
     * Per-section counts of new / unopened records for the admin sidebar badges.
     * Each clears as the admin processes the items (opens the job listing, verifies
     * the company, reviews the application, opens the request, reads the message).
     *
     * @return array{jobs:int,applications:int,companies:int,recruitment:int,contacts:int,chat:int}
     */
    public static function sidebarCounts(): array
    {
        return [
            // New job listings the admin hasn't opened yet (contract staffing is
            // managed elsewhere, so it's excluded to match the jobs list).
            'jobs' => Schema::hasTable('job_listings')
                ? JobListing::regularJobs()->whereNull('admin_viewed_at')->count() : 0,
            // New applications not yet actioned (status set to 'applied' on submit).
            'applications' => Schema::hasTable('job_applications')
                ? JobApplication::where('status', 'applied')->count() : 0,
            // New companies the admin hasn't verified yet.
            'companies' => Schema::hasTable('companies')
                ? Company::where('is_verified', false)->count() : 0,
            // Hiring/CV requests the admin hasn't opened yet.
            'recruitment' => Schema::hasTable('recruitment_requests')
                ? RecruitmentRequest::where('status', 'pending')->count() : 0,
            // Contact submissions the admin hasn't opened yet.
            'contacts' => Schema::hasTable('contact_submissions')
                ? ContactSubmission::whereNull('admin_viewed_at')->count() : 0,
            // Unread chat messages addressed to the admin (cleared when the
            // conversation is opened — see Admin\ChatController::index()).
            'chat' => Schema::hasTable('chat_messages')
                ? ChatMessage::query()
                    ->whereNull('read_at')
                    ->where('sender_role', '!=', ChatMessage::ROLE_ADMIN)
                    ->whereHas('conversation', fn ($q) => $q->whereIn('type', [
                        ChatConversation::TYPE_ADMIN_CANDIDATE,
                        ChatConversation::TYPE_ADMIN_EMPLOYER,
                    ]))
                    ->count()
                : 0,
        ];
    }

    /**
     * Full feed for the notifications page: each category plus its most recent
     * pending records.
     *
     * @return array<int, array{key:string,label:string,count:int,url:string,items:array}>
     */
    public static function feed(int $perCategory = 6): array
    {
        $feed = [];

        if (Schema::hasTable('job_listings')) {
            $feed[] = self::category('jobs', 'Jobs pending approval', route('admin.jobs', ['status' => 'pending']),
                JobListing::where('status', 'pending')->count(),
                JobListing::with('company')->where('status', 'pending')->latest()->limit($perCategory)->get()
                    ->map(fn (JobListing $j) => [
                        'title' => $j->title ?: 'Untitled job',
                        'meta' => $j->company?->name ?? 'No company',
                        'time' => $j->created_at,
                        'url' => route('admin.jobs.show', $j),
                    ])->all()
            );
        }

        if (Schema::hasTable('payments')) {
            $feed[] = self::category('payments', 'Payments awaiting confirmation', route('admin.payments', ['status' => 'pending']),
                Payment::where('status', 'pending')->count(),
                Payment::with('order')->where('status', 'pending')->latest()->limit($perCategory)->get()
                    ->map(fn (Payment $p) => [
                        'title' => $p->order?->order_number ? 'Order '.$p->order->order_number : 'Payment #'.$p->id,
                        'meta' => trim(strtoupper((string) $p->currency).' '.number_format((float) $p->amount, 2).' · '.($p->gateway ?? '')),
                        'time' => $p->created_at,
                        'url' => route('admin.payments.show', $p),
                    ])->all()
            );
        }

        if (Schema::hasTable('recruitment_requests')) {
            $feed[] = self::category('recruitment', 'New hiring requests', route('admin.recruitment-requests.index', ['status' => 'pending']),
                RecruitmentRequest::where('status', 'pending')->count(),
                RecruitmentRequest::with('company')->where('status', 'pending')->latest()->limit($perCategory)->get()
                    ->map(fn (RecruitmentRequest $r) => [
                        'title' => $r->job_title ?: (ucfirst(str_replace('_', ' ', (string) $r->service_type)) ?: 'Hiring request'),
                        'meta' => $r->company?->name ?? 'Company',
                        'time' => $r->created_at,
                        'url' => route('admin.recruitment-requests.show', $r),
                    ])->all()
            );
        }

        if (Schema::hasTable('contact_submissions')) {
            $feed[] = self::category('contacts', 'New contact messages', route('admin.contacts.index', ['status' => 'new']),
                ContactSubmission::where('status', 'new')->count(),
                ContactSubmission::where('status', 'new')->latest()->limit($perCategory)->get()
                    ->map(fn (ContactSubmission $c) => [
                        'title' => $c->subject ?: 'New message',
                        'meta' => trim(($c->name ?? '').($c->email ? ' · '.$c->email : '')),
                        'time' => $c->created_at,
                        'url' => route('admin.contacts.show', $c),
                    ])->all()
            );
        }

        return $feed;
    }

    private static function category(string $key, string $label, string $url, int $count, array $items): array
    {
        return compact('key', 'label', 'url', 'count', 'items');
    }
}
