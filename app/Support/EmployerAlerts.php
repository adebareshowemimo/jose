<?php

namespace App\Support;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\Payment;
use App\Models\RecruitmentRequest;
use Illuminate\Support\Facades\Schema;

/**
 * Unread-style sidebar badge counts for an employer (the company owner). Each
 * count is "new activity the employer hasn't reviewed" and clears as they open
 * the relevant screen — mirrors AdminAlerts but scoped to one company/owner.
 *
 * Counts guard on hasColumn so a server whose employer_viewed_at migration hasn't
 * run yet shows no badge instead of 500-ing the employer dashboard.
 */
class EmployerAlerts
{
    /**
     * @return array{applicants:int,hiring:int,messages:int,billing:int}
     */
    public static function sidebarCounts(?Company $company, int $userId): array
    {
        if (! $company) {
            return ['applicants' => 0, 'hiring' => 0, 'messages' => 0, 'billing' => 0];
        }

        return [
            'applicants' => self::applicants($company),
            'hiring' => self::hiring($company),
            'messages' => self::messages($company, $userId),
            'billing' => self::billing($userId),
        ];
    }

    // New applications to the company's jobs the employer hasn't opened yet.
    protected static function applicants(Company $company): int
    {
        if (! Schema::hasColumn('job_applications', 'employer_viewed_at')) {
            return 0;
        }

        return JobApplication::whereNull('employer_viewed_at')
            ->whereHas('jobListing', fn ($q) => $q->where('company_id', $company->id))
            ->count();
    }

    // Hiring requests with admin activity (status moves) since the employer last looked.
    protected static function hiring(Company $company): int
    {
        if (! Schema::hasColumn('recruitment_requests', 'employer_viewed_at')) {
            return 0;
        }

        return RecruitmentRequest::where('company_id', $company->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where(function ($q) {
                $q->whereNull('employer_viewed_at')
                  ->orWhereColumn('employer_viewed_at', '<', 'updated_at');
            })
            ->count();
    }

    // Unread chat addressed to the employer — candidate chats + admin support.
    protected static function messages(Company $company, int $userId): int
    {
        if (! Schema::hasTable('chat_messages') || ! Schema::hasTable('chat_conversations')) {
            return 0;
        }

        return ChatMessage::whereNull('read_at')
            ->where('sender_user_id', '!=', $userId)
            ->whereHas('conversation', function ($q) use ($company) {
                $q->where('company_id', $company->id)
                  ->whereIn('type', [
                      ChatConversation::TYPE_EMPLOYER_CANDIDATE,
                      ChatConversation::TYPE_ADMIN_EMPLOYER,
                  ]);
            })
            ->count();
    }

    // Payments needing attention: newly finalized (and unseen since the change),
    // or carrying a receipt the employer hasn't opened yet. Counted per payment.
    protected static function billing(int $userId): int
    {
        if (! Schema::hasColumn('payments', 'employer_viewed_at')) {
            return 0;
        }

        $receiptsTracked = Schema::hasColumn('receipts', 'employer_viewed_at');

        return Payment::whereHas('order', fn ($o) => $o->where('user_id', $userId))
            ->where(function ($q) use ($receiptsTracked) {
                $q->where(function ($q2) {
                    $q2->whereIn('status', ['completed', 'failed', 'refunded'])
                       ->where(function ($q3) {
                           $q3->whereNull('employer_viewed_at')
                              ->orWhereColumn('employer_viewed_at', '<', 'updated_at');
                       });
                });
                if ($receiptsTracked) {
                    $q->orWhereHas('receipt', fn ($r) => $r->whereNull('employer_viewed_at'));
                }
            })
            ->count();
    }
}
