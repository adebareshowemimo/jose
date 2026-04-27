<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function store(Request $request, EmailDispatcher $emails)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $submission = ContactSubmission::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'category' => $data['subject'],
            'message' => $data['message'],
            'status' => 'new',
        ]);

        $submission->chronologicalMessages()->create([
            'sender_type' => 'user',
            'sender_name' => $submission->name,
            'sender_email' => $submission->email,
            'body' => $submission->message,
        ]);

        $vars = $this->templateVars($submission);

        $emails->send('contact.auto_response', $submission->email, $vars);
        $emails->send('contact.admin_notification', $this->adminEmail(), $vars);

        return back()->with('success', 'Thanks for contacting us. Your message has been received and our team will respond shortly.');
    }

    public function thread(string $token)
    {
        $submission = ContactSubmission::with('chronologicalMessages')
            ->where('reply_token', $token)
            ->firstOrFail();

        return view('pages.contact.thread', [
            'pageTitle' => 'Contact Conversation',
            'pageDescription' => "Conversation: {$submission->subject}",
            'submission' => $submission,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Contact', 'url' => route('contact.index')],
                ['label' => 'Conversation'],
            ],
        ]);
    }

    public function reply(Request $request, string $token, EmailDispatcher $emails)
    {
        $submission = ContactSubmission::where('reply_token', $token)->firstOrFail();

        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $submission->chronologicalMessages()->create([
            'sender_type' => 'user',
            'sender_name' => $submission->name,
            'sender_email' => $submission->email,
            'body' => $data['message'],
        ]);

        $submission->update([
            'status' => 'customer_replied',
            'closed_at' => null,
        ]);

        $emails->send('contact.user_reply_notification', $this->adminEmail(), array_merge($this->templateVars($submission), [
            'reply_message' => $data['message'],
        ]));

        return back()->with('success', 'Your reply has been sent to our team.');
    }

    private function templateVars(ContactSubmission $submission): array
    {
        return [
            'contact_id' => $submission->id,
            'name' => $submission->name,
            'email' => $submission->email,
            'phone' => $submission->phone ?? 'Not provided',
            'subject' => $submission->subject,
            'category' => $submission->category,
            'message' => $submission->message,
            'admin_url' => route('admin.contacts.show', $submission),
            'reply_url' => route('contact.thread', $submission->reply_token),
        ];
    }

    private function adminEmail(): string
    {
        return config('mail.admin_address')
            ?? config('mail.from.address')
            ?? 'info@joseoceanjobs.com';
    }
}
