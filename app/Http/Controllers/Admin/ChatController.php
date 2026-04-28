<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $conversations = ChatConversation::with(['candidate.user', 'latestMessage.sender'])
            ->where('type', ChatConversation::TYPE_ADMIN_CANDIDATE)
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $selectedConversation = null;
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

            $conversations = ChatConversation::with(['candidate.user', 'latestMessage.sender'])
                ->where('type', ChatConversation::TYPE_ADMIN_CANDIDATE)
                ->orderByDesc('last_message_at')
                ->orderByDesc('created_at')
                ->get();
        } elseif ($request->filled('conversation')) {
            $selectedConversation = $conversations->firstWhere('id', $request->integer('conversation'));
        } else {
            $selectedConversation = $conversations->first();
        }

        if ($selectedConversation) {
            $selectedConversation->load(['candidate.user', 'messages.sender']);
            $selectedConversation->messages()
                ->where('sender_user_id', '!=', $request->user()->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            $messages = $selectedConversation->messages()->with('sender')->oldest()->get();
        } else {
            $messages = collect();
        }

        return view('admin.chat.index', compact('conversations', 'selectedConversation', 'messages'));
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

    public function send(Request $request, ChatConversation $conversation)
    {
        abort_unless($conversation->type === ChatConversation::TYPE_ADMIN_CANDIDATE, 403);

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
