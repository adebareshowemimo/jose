<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Company;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $selectedConversation = null;
        $activeTab = $request->input('tab', 'candidates');

        if ($request->filled('candidate_id')) {
            $candidate = Candidate::whereHas('user')->findOrFail($request->integer('candidate_id'));
            $selectedConversation = ChatConversation::firstOrCreate(
                [
                    'type' => ChatConversation::TYPE_ADMIN_CANDIDATE,
                    'candidate_id' => $candidate->id,
                    'company_id' => null,
                    'recruitment_request_candidate_id' => null,
                ],
                [
                    'started_by_user_id' => $request->user()->id,
                    'last_message_at' => now(),
                ]
            );
            $activeTab = 'candidates';
        } elseif ($request->filled('company_id')) {
            $company = Company::findOrFail($request->integer('company_id'));
            $selectedConversation = ChatConversation::firstOrCreate(
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
            $activeTab = 'employers';
        }

        $candidateConversations = ChatConversation::with(['candidate.user', 'latestMessage.sender'])
            ->where('type', ChatConversation::TYPE_ADMIN_CANDIDATE)
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $employerConversations = ChatConversation::with(['company.owner', 'latestMessage.sender'])
            ->where('type', ChatConversation::TYPE_ADMIN_EMPLOYER)
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        if (! $selectedConversation && $request->filled('conversation')) {
            $conversationId = $request->integer('conversation');
            $selectedConversation = $candidateConversations->firstWhere('id', $conversationId)
                ?? $employerConversations->firstWhere('id', $conversationId);
        }

        if (! $selectedConversation && ! $request->hasAny(['candidate_id', 'company_id', 'conversation'])) {
            $selectedConversation = $activeTab === 'employers'
                ? $employerConversations->first()
                : $candidateConversations->first();
        }

        if ($selectedConversation) {
            $activeTab = $selectedConversation->type === ChatConversation::TYPE_ADMIN_EMPLOYER
                ? 'employers'
                : 'candidates';

            $selectedConversation->load(['candidate.user', 'company.owner', 'messages.sender']);
            $selectedConversation->messages()
                ->where('sender_user_id', '!=', $request->user()->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            $messages = $selectedConversation->messages()->with('sender')->oldest()->get();
        } else {
            $messages = collect();
        }

        return view('admin.chat.index', [
            'candidateConversations' => $candidateConversations,
            'employerConversations' => $employerConversations,
            'selectedConversation' => $selectedConversation,
            'messages' => $messages,
            'activeTab' => $activeTab,
        ]);
    }

    public function searchCandidates(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $candidates = Candidate::with(['user:id,name,email,avatar', 'resumes:id,candidate_id,file_path'])
            ->whereHas('user', function ($q) use ($query) {
                if ($query !== '') {
                    $q->where(function ($inner) use ($query) {
                        $inner->where('name', 'like', "%{$query}%")
                              ->orWhere('email', 'like', "%{$query}%");
                    });
                }
            })
            ->orderByDesc('updated_at')
            ->take(15)
            ->get();

        return response()->json([
            'results' => $candidates->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->user?->name ?? 'Candidate',
                'email' => $c->user?->email,
                'avatar' => $c->user?->avatar,
                'profile_url' => $c->slug ? route('candidate.detail', $c->slug) : null,
                'has_cv' => $c->resumes->contains(fn ($r) => ! empty($r->file_path)),
            ])->values(),
        ]);
    }

    public function searchEmployers(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $companies = Company::with(['owner:id,name,email,avatar'])
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%")
                          ->orWhereHas('owner', function ($ownerQuery) use ($query) {
                              $ownerQuery->where('name', 'like', "%{$query}%")
                                         ->orWhere('email', 'like', "%{$query}%");
                          });
                });
            })
            ->orderByDesc('updated_at')
            ->take(15)
            ->get();

        return response()->json([
            'results' => $companies->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name ?? 'Company',
                'email' => $c->email ?? $c->owner?->email,
                'owner_name' => $c->owner?->name,
                'logo' => $c->logo,
            ])->values(),
        ]);
    }

    public function send(Request $request, ChatConversation $conversation)
    {
        abort_unless(in_array($conversation->type, [
            ChatConversation::TYPE_ADMIN_CANDIDATE,
            ChatConversation::TYPE_ADMIN_EMPLOYER,
        ], true), 403);

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $message = $conversation->messages()->create([
            'sender_user_id' => $request->user()->id,
            'sender_role' => ChatMessage::ROLE_ADMIN,
            'body' => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        try {
            event(new ChatMessageSent($message));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Message sent.');
    }
}
