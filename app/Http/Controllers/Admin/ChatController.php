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
        $candidateSearch = Candidate::with(['user', 'location'])
            ->whereHas('user', function ($query) use ($request) {
                if ($request->filled('search')) {
                    $search = (string) $request->input('search');
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                }
            })
            ->orderByDesc('updated_at')
            ->take(25)
            ->get();

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
        } elseif ($request->filled('conversation')) {
            $selectedConversation = $conversations->firstWhere('id', $request->integer('conversation'));
        } else {
            $selectedConversation = $conversations->first();
        }

        $conversations = ChatConversation::with(['candidate.user', 'latestMessage.sender'])
            ->where('type', ChatConversation::TYPE_ADMIN_CANDIDATE)
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

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

        return view('admin.chat.index', compact('candidateSearch', 'conversations', 'selectedConversation', 'messages'));
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
