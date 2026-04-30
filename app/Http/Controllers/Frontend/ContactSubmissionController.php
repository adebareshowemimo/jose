<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Support\ContactRoutes;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function store(Request $request, EmailDispatcher $emails, ContactRoutes $routes)
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
        $teamEmail = $routes->routeFor($submission->subject);

        $emails->send('contact.auto_response', $submission->email, $vars, $teamEmail);
        $emails->send('contact.admin_notification', $teamEmail, $vars, $submission->email);

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

    public function reply(Request $request, string $token, EmailDispatcher $emails, ContactRoutes $routes)
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

        $vars = array_merge($this->templateVars($submission), [
            'reply_message' => $data['message'],
        ]);

        $emails->send(
            'contact.user_reply_notification',
            $routes->routeFor($submission->subject),
            $vars,
            $submission->email,
        );

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

}
