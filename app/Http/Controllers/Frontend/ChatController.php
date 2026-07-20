<?php

namespace App\Http\Controllers\Frontend;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\JobApplication;
use App\Models\RecruitmentRequestCandidate;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function employer(Request $request)
    {
        $company = $request->user()->company;

        if (! $company) {
            return view('pages.dashboard.employer.messages', [
                'dashboardType' => 'employer',
                'conversations' => collect(),
                'adminConversation' => null,
                'selectedConversation' => null,
                'messages' => collect(),
            ]);
        }

        $this->syncEmployerConversations($company->id, $request->user()->id);

        $conversations = ChatConversation::with([
                'candidate.user',
                'recruitmentRequestCandidate.recruitmentRequest',
                'jobApplication.jobListing',
                'latestMessage.sender',
            ])
            ->where('type', ChatConversation::TYPE_EMPLOYER_CANDIDATE)
            ->where('company_id', $company->id)
            ->where(fn ($query) => $query
                ->whereNotNull('recruitment_request_candidate_id')
                ->orWhereHas('jobApplication', fn ($application) => $application
                    ->whereNotNull('employer_chat_access_granted_at')))
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $adminConversation = ChatConversation::with(['latestMessage.sender'])
            ->where('type', ChatConversation::TYPE_ADMIN_EMPLOYER)
            ->where('company_id', $company->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->first();

        $selectableConversations = $adminConversation
            ? $conversations->concat([$adminConversation])
            : $conversations;

        $selectedConversation = $this->selectedConversation($request, $selectableConversations);
        $messages = $selectedConversation ? $this->openConversation($selectedConversation, $request->user()->id) : collect();

        return view('pages.dashboard.employer.messages', compact('conversations', 'adminConversation', 'selectedConversation', 'messages') + [
            'dashboardType' => 'employer',
        ]);
    }

    public function contactAdmin(Request $request)
    {
        $company = $request->user()->company;

        if (! $company) {
            return redirect()->route('employer.company.profile')
                ->with('error', 'Please set up your company profile before contacting admin support.');
        }

        $conversation = ChatConversation::firstOrCreate(
            [
                'type' => ChatConversation::TYPE_ADMIN_EMPLOYER,
                'company_id' => $company->id,
                'candidate_id' => null,
                'recruitment_request_candidate_id' => null,
            ],
            [
                'started_by_user_id' => $request->user()->id,
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('employer.chat', ['conversation' => $conversation->id]);
    }

    public function candidate(Request $request)
    {
        $candidate = $request->user()->candidate;

        if (! $candidate) {
            return view('pages.dashboard.candidate.messages', [
                'dashboardType' => 'candidate',
                'conversations' => collect(),
                'selectedConversation' => null,
                'messages' => collect(),
            ]);
        }

        $this->syncCandidateConversations($candidate->id, $request->user()->id);

        $conversations = ChatConversation::with([
                'company',
                'candidate.user',
                'recruitmentRequestCandidate.recruitmentRequest',
                'jobApplication.jobListing',
                'latestMessage.sender',
            ])
            ->where('candidate_id', $candidate->id)
            ->whereIn('type', [ChatConversation::TYPE_EMPLOYER_CANDIDATE, ChatConversation::TYPE_ADMIN_CANDIDATE])
            ->where(fn ($query) => $query
                ->where('type', ChatConversation::TYPE_ADMIN_CANDIDATE)
                ->orWhereNotNull('recruitment_request_candidate_id')
                ->orWhereHas('jobApplication', fn ($application) => $application
                    ->whereNotNull('employer_chat_access_granted_at')))
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $selectedConversation = $this->selectedConversation($request, $conversations);
        $messages = $selectedConversation ? $this->openConversation($selectedConversation, $request->user()->id) : collect();

        return view('pages.dashboard.candidate.messages', compact('conversations', 'selectedConversation', 'messages') + [
            'dashboardType' => 'candidate',
        ]);
    }

    public function sendEmployerMessage(Request $request, ChatConversation $conversation)
    {
        $this->authorizeEmployer($request, $conversation, allowAdmin: true);

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $this->createMessage($conversation, $request->user(), ChatMessage::ROLE_EMPLOYER, $data['body']);

        return back()->with('success', 'Message sent.');
    }

    public function sendCandidateMessage(Request $request, ChatConversation $conversation)
    {
        $this->authorizeCandidate($request, $conversation);

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $this->createMessage($conversation, $request->user(), ChatMessage::ROLE_CANDIDATE, $data['body']);

        return back()->with('success', 'Message sent.');
    }

    public function scheduleInterview(Request $request, ChatConversation $conversation, EmailDispatcher $dispatcher)
    {
        $this->authorizeEmployer($request, $conversation);

        $data = $request->validate([
            'interview_date' => 'required|string|max:255',
            'interview_location' => 'required|string|max:255',
            'note' => 'nullable|string|max:2000',
        ]);

        $body = "Interview scheduled\nWhen: {$data['interview_date']}\nWhere: {$data['interview_location']}";
        if (! empty($data['note'])) {
            $body .= "\n\n".$data['note'];
        }

        $message = $this->createMessage($conversation, $request->user(), ChatMessage::ROLE_EMPLOYER, $body, 'schedule_interview', $data);
        $conversation->recruitmentRequestCandidate?->update(['employer_decision' => 'contacted']);
        $this->emailCandidate($conversation, $dispatcher, 'chat.interview_scheduled', array_merge($this->emailVars($conversation), $data));

        return back()->with('success', 'Interview details sent.');
    }

    public function requestDocuments(Request $request, ChatConversation $conversation, EmailDispatcher $dispatcher)
    {
        $this->authorizeEmployer($request, $conversation);

        $data = $request->validate([
            'documents' => 'required|string|max:2000',
            'note' => 'nullable|string|max:2000',
        ]);

        $body = "Documents requested\n{$data['documents']}";
        if (! empty($data['note'])) {
            $body .= "\n\n".$data['note'];
        }

        $this->createMessage($conversation, $request->user(), ChatMessage::ROLE_EMPLOYER, $body, 'request_documents', $data);
        $this->emailCandidate($conversation, $dispatcher, 'chat.documents_requested', array_merge($this->emailVars($conversation), $data));

        return back()->with('success', 'Document request sent.');
    }

    public function sendOffer(Request $request, ChatConversation $conversation, EmailDispatcher $dispatcher)
    {
        $this->authorizeEmployer($request, $conversation);

        $data = $request->validate([
            'offer_title' => 'required|string|max:255',
            'offer_details' => 'required|string|max:3000',
            'final_offer' => 'nullable|boolean',
        ]);

        $data['final_offer'] = $request->boolean('final_offer');
        $body = "Offer sent: {$data['offer_title']}\n\n{$data['offer_details']}";

        $this->createMessage($conversation, $request->user(), ChatMessage::ROLE_EMPLOYER, $body, 'send_offer', $data);

        if ($data['final_offer']) {
            $conversation->recruitmentRequestCandidate?->update(['employer_decision' => 'hired']);
        }

        $this->emailCandidate($conversation, $dispatcher, 'chat.offer_sent', array_merge($this->emailVars($conversation), $data));

        return back()->with('success', 'Offer sent.');
    }

    /**
     * Employer invites a delivered candidate to a meeting / pre-screening interview
     * straight from the candidate profile. Posts into the existing employer↔candidate
     * thread and emails the candidate. Only candidates delivered by admin are allowed.
     */
    public function invite(Request $request, Candidate $candidate, EmailDispatcher $dispatcher)
    {
        $company = $request->user()->company;

        if (! $company) {
            return redirect()->route('employer.company.profile')
                ->with('error', 'Set up your company profile before inviting candidates.');
        }

        $data = $request->validate([
            'invite_type' => 'required|in:pre_screening,interview,meeting',
            'scheduled_at' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'note' => 'nullable|string|max:2000',
        ]);

        $conversation = ChatConversation::where('type', ChatConversation::TYPE_EMPLOYER_CANDIDATE)
            ->where('company_id', $company->id)
            ->where('candidate_id', $candidate->id)
            ->orderByDesc('last_message_at')
            ->first();

        // No thread yet but a delivery exists → create it (mirrors syncEmployerConversations).
        if (! $conversation) {
            $delivery = RecruitmentRequestCandidate::where('candidate_id', $candidate->id)
                ->whereHas('recruitmentRequest', fn ($q) => $q->where('company_id', $company->id))
                ->latest('delivered_at')
                ->first();

            if ($delivery) {
                $conversation = ChatConversation::firstOrCreate(
                    [
                        'type' => ChatConversation::TYPE_EMPLOYER_CANDIDATE,
                        'recruitment_request_candidate_id' => $delivery->id,
                    ],
                    [
                        'company_id' => $company->id,
                        'candidate_id' => $candidate->id,
                        'started_by_user_id' => $request->user()->id,
                        'last_message_at' => now(),
                    ]
                );
            }
        }

        if (! $conversation) {
            return back()->with('error', 'You can only invite candidates that have been delivered to you.');
        }

        $labels = ['pre_screening' => 'Pre-screening interview', 'interview' => 'Interview', 'meeting' => 'Meeting'];
        $label = $labels[$data['invite_type']];

        $body = "{$label} invitation\nWhen: {$data['scheduled_at']}\nWhere: {$data['location']}";
        if (! empty($data['note'])) {
            $body .= "\n\n{$data['note']}";
        }

        $this->createMessage($conversation, $request->user(), ChatMessage::ROLE_EMPLOYER, $body, 'schedule_interview', [
            'invite_type' => $data['invite_type'],
            'interview_date' => $data['scheduled_at'],
            'interview_location' => $data['location'],
            'note' => $data['note'] ?? null,
        ]);

        $conversation->recruitmentRequestCandidate?->update(['employer_decision' => 'contacted']);

        $this->emailCandidate($conversation, $dispatcher, 'chat.interview_scheduled', array_merge($this->emailVars($conversation), [
            'interview_date' => $data['scheduled_at'],
            'interview_location' => $data['location'],
            'note' => $data['note'] ?? '',
        ]));

        return redirect()->route('employer.chat', ['conversation' => $conversation->id])
            ->with('success', $label.' invitation sent to '.($candidate->user?->name ?? 'the candidate').'.');
    }

    private function syncEmployerConversations(int $companyId, int $userId): void
    {
        RecruitmentRequestCandidate::query()
            ->whereNotNull('candidate_id')
            ->whereHas('recruitmentRequest', fn ($q) => $q->where('company_id', $companyId))
            ->with('recruitmentRequest')
            ->get()
            ->each(function (RecruitmentRequestCandidate $assignment) use ($companyId, $userId) {
                ChatConversation::firstOrCreate(
                    [
                        'type' => ChatConversation::TYPE_EMPLOYER_CANDIDATE,
                        'recruitment_request_candidate_id' => $assignment->id,
                    ],
                    [
                        'company_id' => $companyId,
                        'candidate_id' => $assignment->candidate_id,
                        'started_by_user_id' => $userId,
                        'last_message_at' => $assignment->delivered_at ?? now(),
                    ]
                );
            });

        JobApplication::query()
            ->whereNotNull('employer_chat_access_granted_at')
            ->whereHas('jobListing', fn ($query) => $query
                ->where('company_id', $companyId)
                ->where('is_approved', true))
            ->with('jobListing')
            ->get()
            ->each(function (JobApplication $application) use ($companyId, $userId) {
                ChatConversation::firstOrCreate(
                    [
                        'type' => ChatConversation::TYPE_EMPLOYER_CANDIDATE,
                        'job_application_id' => $application->id,
                    ],
                    [
                        'company_id' => $companyId,
                        'candidate_id' => $application->candidate_id,
                        'recruitment_request_candidate_id' => null,
                        'started_by_user_id' => $userId,
                        'last_message_at' => $application->created_at ?? now(),
                    ]
                );
            });
    }

    private function syncCandidateConversations(int $candidateId, int $userId): void
    {
        RecruitmentRequestCandidate::query()
            ->where('candidate_id', $candidateId)
            ->whereHas('recruitmentRequest')
            ->with('recruitmentRequest')
            ->get()
            ->each(function (RecruitmentRequestCandidate $assignment) use ($userId) {
                ChatConversation::firstOrCreate(
                    [
                        'type' => ChatConversation::TYPE_EMPLOYER_CANDIDATE,
                        'recruitment_request_candidate_id' => $assignment->id,
                    ],
                    [
                        'company_id' => $assignment->recruitmentRequest->company_id,
                        'candidate_id' => $assignment->candidate_id,
                        'started_by_user_id' => $userId,
                        'last_message_at' => $assignment->delivered_at ?? now(),
                    ]
                );
            });

        JobApplication::query()
            ->where('candidate_id', $candidateId)
            ->whereNotNull('employer_chat_access_granted_at')
            ->whereHas('jobListing', fn ($query) => $query->where('is_approved', true))
            ->with('jobListing')
            ->get()
            ->each(function (JobApplication $application) use ($userId) {
                ChatConversation::firstOrCreate(
                    [
                        'type' => ChatConversation::TYPE_EMPLOYER_CANDIDATE,
                        'job_application_id' => $application->id,
                    ],
                    [
                        'company_id' => $application->jobListing->company_id,
                        'candidate_id' => $application->candidate_id,
                        'recruitment_request_candidate_id' => null,
                        'started_by_user_id' => $userId,
                        'last_message_at' => $application->created_at ?? now(),
                    ]
                );
            });
    }

    private function selectedConversation(Request $request, $conversations): ?ChatConversation
    {
        $selectedId = $request->integer('conversation');
        return $selectedId
            ? $conversations->firstWhere('id', $selectedId)
            : $conversations->first();
    }

    private function openConversation(ChatConversation $conversation, int $userId)
    {
        $conversation->messages()
            ->where('sender_user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $conversation->messages()->with('sender')->oldest()->get();
    }

    private function authorizeEmployer(Request $request, ChatConversation $conversation, bool $allowAdmin = false): void
    {
        $allowedTypes = $allowAdmin
            ? [ChatConversation::TYPE_EMPLOYER_CANDIDATE, ChatConversation::TYPE_ADMIN_EMPLOYER]
            : [ChatConversation::TYPE_EMPLOYER_CANDIDATE];

        abort_unless(
            in_array($conversation->type, $allowedTypes, true)
            && $request->user()->role?->name === 'employer'
            && (int) $request->user()->company?->id === (int) $conversation->company_id,
            403
        );

        if ($conversation->job_application_id) {
            abort_unless($conversation->jobApplication?->employer_chat_access_granted_at, 403);
        }
    }

    private function authorizeCandidate(Request $request, ChatConversation $conversation): void
    {
        abort_unless(
            $request->user()->role?->name === 'candidate'
            && (int) $request->user()->candidate?->id === (int) $conversation->candidate_id,
            403
        );

        if ($conversation->job_application_id) {
            abort_unless($conversation->jobApplication?->employer_chat_access_granted_at, 403);
        }
    }

    private function createMessage(ChatConversation $conversation, $user, string $role, string $body, ?string $actionType = null, ?array $payload = null): ChatMessage
    {
        $message = $conversation->messages()->create([
            'sender_user_id' => $user->id,
            'sender_role' => $role,
            'body' => $body,
            'action_type' => $actionType,
            'action_payload' => $payload,
        ]);

        $conversation->update(['last_message_at' => now()]);
        $this->broadcastMessage($message);

        return $message;
    }

    private function broadcastMessage(ChatMessage $message): void
    {
        try {
            event(new ChatMessageSent($message));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function emailCandidate(ChatConversation $conversation, EmailDispatcher $dispatcher, string $template, array $vars): void
    {
        $user = $conversation->candidate?->user;
        if ($user?->email) {
            $dispatcher->send($template, $user, $vars);
        }
    }

    private function emailVars(ChatConversation $conversation): array
    {
        return [
            'name' => $conversation->candidate?->user?->name ?? 'Candidate',
            'candidate_name' => $conversation->candidate?->user?->name ?? 'Candidate',
            'company_name' => $conversation->company?->name ?? config('app.name'),
            'job_title' => $conversation->jobApplication?->jobListing?->title
                ?? $conversation->recruitmentRequestCandidate?->recruitmentRequest?->job_title
                ?? 'Recruitment opportunity',
            'chat_url' => route('user.chat', ['conversation' => $conversation->id]),
        ];
    }
}
