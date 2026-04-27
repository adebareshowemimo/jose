<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactSubmission::query();

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $contacts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('admin.contacts.create', [
            'contact' => new ContactSubmission(),
        ]);
    }

    public function store(Request $request)
    {
        $contact = ContactSubmission::create($this->validatedData($request));

        $contact->chronologicalMessages()->create([
            'sender_type' => 'user',
            'sender_name' => $contact->name,
            'sender_email' => $contact->email,
            'body' => $contact->message,
        ]);

        return redirect()->route('admin.contacts.show', $contact)->with('success', 'Contact submission created.');
    }

    public function show(ContactSubmission $contact)
    {
        $contact->load('chronologicalMessages');

        return view('admin.contacts.show', compact('contact'));
    }

    public function edit(ContactSubmission $contact)
    {
        return view('admin.contacts.edit', compact('contact'));
    }

    public function update(Request $request, ContactSubmission $contact)
    {
        $contact->update($this->validatedData($request));

        return redirect()->route('admin.contacts.show', $contact)->with('success', 'Contact submission updated.');
    }

    public function destroy(ContactSubmission $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact submission deleted.');
    }

    public function respond(Request $request, ContactSubmission $contact, EmailDispatcher $emails)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'status' => ['required', 'in:in_progress,resolved,closed'],
        ]);

        $message = $contact->chronologicalMessages()->create([
            'sender_type' => 'admin',
            'sender_name' => auth()->user()->name ?? 'JCL Team',
            'sender_email' => auth()->user()->email ?? config('mail.from.address'),
            'body' => $data['message'],
        ]);

        $sent = $emails->send('contact.admin_reply', $contact->email, [
            'contact_id' => $contact->id,
            'name' => $contact->name,
            'email' => $contact->email,
            'subject' => $contact->subject,
            'message' => $contact->message,
            'response' => $data['message'],
            'reply_url' => route('contact.thread', $contact->reply_token),
        ]);

        $message->update(['emailed_at' => $sent ? now() : null]);
        $contact->update([
            'status' => $data['status'],
            'last_responded_at' => now(),
            'closed_at' => $data['status'] === 'closed' ? now() : null,
        ]);

        return back()->with($sent ? 'success' : 'error', $sent ? 'Response sent to user.' : 'Response saved, but email failed. Check logs.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
            'status' => ['required', 'in:new,in_progress,customer_replied,resolved,closed,spam'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
        ]);
    }
}
