<?php

namespace App\Support;

use App\Events\AdminNotification;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\RecruitmentRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Turns a freshly-created record into a live admin notification and pushes it to the
 * `admin.notifications` private channel. The on-screen badge counts ride along in the
 * payload (reusing {@see AdminAlerts}) so a pushed update is identical to a page reload.
 *
 * Each category key matches a badge in {@see AdminAlerts::sidebarCounts()}.
 */
class AdminNotifier
{
    /**
     * Build the payload for a created record and broadcast it. Wrapped so a broadcasting
     * failure (e.g. Reverb not running) can never break the request that created the
     * record — it is logged and swallowed instead.
     */
    public static function broadcast(string $category, Model $record): void
    {
        try {
            [$label, $title, $meta, $url] = self::describe($category, $record);

            AdminNotification::dispatch([
                'category' => $category,
                'label' => $label,
                'title' => $title,
                'meta' => $meta,
                'url' => $url,
                'time' => now()->toIso8601String(),
                'counts' => [
                    'sidebar' => AdminAlerts::sidebarCounts(),
                    'bellTotal' => AdminAlerts::total(),
                    'categories' => AdminAlerts::categories(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Admin notification broadcast failed: '.$e->getMessage(), [
                'category' => $category,
                'record' => method_exists($record, 'getKey') ? $record->getKey() : null,
            ]);
        }
    }

    /**
     * Map a record to [label, title, meta, deep-link]. Display fields mirror
     * {@see AdminAlerts::feed()} so toasts read the same as the notifications page.
     *
     * @return array{0:string,1:string,2:string,3:string}
     */
    private static function describe(string $category, Model $record): array
    {
        return match ($category) {
            'jobs' => [
                'job pending approval',
                $record->title ?: 'Untitled job',
                $record->company?->name ?? 'No company',
                route('admin.jobs.show', $record),
            ],
            'applications' => self::application($record, 'application', route('admin.applications')),
            'contractStaffing' => self::application($record, 'contract application', route('admin.contract-staffing.index')),
            'companies' => [
                'company',
                $record->name ?: 'New company',
                $record->owner?->name ?? $record->email ?? '',
                route('admin.companies.show', $record),
            ],
            'recruitment' => [
                'hiring request',
                $record->job_title ?: (RecruitmentRequest::SERVICE_TYPES[$record->service_type] ?? 'Hiring request'),
                $record->company?->name ?? 'Company',
                route('admin.recruitment-requests.show', $record),
            ],
            'contacts' => [
                'contact message',
                $record->subject ?: 'New message',
                trim(($record->name ?? '').($record->email ? ' · '.$record->email : '')),
                route('admin.contacts.show', $record),
            ],
            'orders' => [
                'order',
                $record->order_number ? 'Order '.$record->order_number : 'Order #'.$record->getKey(),
                trim(strtoupper((string) $record->currency).' '.number_format((float) $record->total, 2)),
                route('admin.orders.show', $record),
            ],
            'payments' => [
                'payment',
                $record->order?->order_number ? 'Order '.$record->order->order_number : 'Payment #'.$record->getKey(),
                trim(strtoupper((string) $record->currency).' '.number_format((float) $record->amount, 2)),
                route('admin.payments.show', $record),
            ],
            'events' => [
                'event registration',
                $record->event?->title ?? 'Event registration',
                trim(($record->buyer_name ?? '').($record->buyer_email ? ' · '.$record->buyer_email : '')),
                route('admin.events.registrations', $record->event_id),
            ],
            'training' => [
                'training enrolment',
                $record->program?->title ?? 'Training enrolment',
                $record->user?->name ?? '',
                route('admin.training.index'),
            ],
            'newsletter' => [
                'newsletter subscriber',
                $record->email ?: 'New subscriber',
                $record->name ?? '',
                route('admin.newsletter.index'),
            ],
            'chat' => [
                'chat message',
                $record->sender?->name ?? 'New message',
                Str::limit((string) $record->body, 60),
                route('admin.chat.index'),
            ],
            default => ['notification', 'New activity', '', route('admin.dashboard')],
        };
    }

    /**
     * Job applications share one shape; only the label/deep-link differ between
     * regular and contract-staffing roles.
     *
     * @return array{0:string,1:string,2:string,3:string}
     */
    private static function application(JobApplication $record, string $label, string $url): array
    {
        return [
            $label,
            $record->jobListing?->title ?? 'Job application',
            $record->candidate?->user?->name ?? 'Candidate',
            $url,
        ];
    }
}
