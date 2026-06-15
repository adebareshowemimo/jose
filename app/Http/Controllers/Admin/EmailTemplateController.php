<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('category')->orderBy('name')->get()->groupBy('category');
        return view('admin.email-templates.index', compact('templates'));
    }

    public function create()
    {
        $categories = EmailTemplate::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.email-templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $template = EmailTemplate::create([
            'key' => $this->uniqueKey($data['name']),
            'name' => $data['name'],
            'category' => $data['category'],
            'subject' => $data['subject'],
            'body_html' => $data['body_html'],
            'is_active' => $request->boolean('is_active'),
            'is_custom' => true,
        ]);

        return redirect()->route('admin.email-templates.edit', $template)
            ->with('success', 'Template created. It is now selectable on recruitment requests.');
    }

    public function cloneTemplate(EmailTemplate $template)
    {
        $copy = $template->replicate(['key']);
        $copy->key = $this->uniqueKey($template->name . ' copy');
        $copy->name = $template->name . ' (Copy)';
        $copy->is_custom = true;
        // Inherit the source's active state via replicate() so a clone of an active
        // template is immediately selectable in the recruitment-request notify modal.
        $copy->save();

        return redirect()->route('admin.email-templates.edit', $copy)
            ->with('success', 'Template cloned. Customise the copy below — it is already selectable on recruitment requests.');
    }

    public function destroy(EmailTemplate $template)
    {
        // Never allow deleting the seeded system templates the app sends transactionally.
        if (! $template->is_custom) {
            return back()->with('error', 'System templates cannot be deleted — only custom templates can.');
        }

        $template->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Custom template deleted.');
    }

    /**
     * Build a unique, namespaced key for an admin-created/cloned template so it
     * never collides with the seeded system keys (e.g. "recruitment.quote_sent").
     */
    protected function uniqueKey(string $name): string
    {
        $base = 'custom.' . (Str::slug($name, '_') ?: 'template');
        $key = $base;
        $i = 1;
        while (EmailTemplate::where('key', $key)->exists()) {
            $key = $base . '_' . $i++;
        }

        return $key;
    }

    public function edit(EmailTemplate $template)
    {
        return view('admin.email-templates.edit', compact('template'));
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $template->update([
            'name' => $data['name'],
            'subject' => $data['subject'],
            'body_html' => $data['body_html'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.email-templates.edit', $template)
            ->with('success', 'Template saved.');
    }

    public function preview(Request $request, EmailTemplate $template, EmailDispatcher $dispatcher)
    {
        $vars = $this->sampleVars($template);
        $rendered = [
            'subject' => $dispatcher->substitute($template->subject, $vars),
            'body' => $dispatcher->substitute($template->body_html, $vars),
        ];
        return response()->view('emails.layouts.master', [
            'subject' => $rendered['subject'],
            'body' => $rendered['body'],
            'appName' => config('app.name', 'JOSEOCEANJOBS'),
        ]);
    }

    public function sendTest(Request $request, EmailTemplate $template, EmailDispatcher $dispatcher)
    {
        $request->validate(['to' => 'required|email']);

        $vars = $this->sampleVars($template);
        $ok = $dispatcher->send($template->key, $request->input('to'), $vars);

        return back()->with($ok ? 'success' : 'error',
            $ok ? "Test email sent to {$request->input('to')}." : 'Failed to send test email — check storage/logs/laravel.log.');
    }

    protected function sampleVars(EmailTemplate $template): array
    {
        $user = Auth::user();
        return [
            'name' => $user?->name ?? 'Sample User',
            'email' => $user?->email ?? 'sample@example.com',
            'app_name' => config('app.name', 'JOSEOCEANJOBS'),
            'app_url' => url('/'),
            'support_email' => 'info@joseoceanjobs.com',
            'verify_url' => url('/email/verify/sample'),
            'reset_url' => url('/auth/reset-password/sample-token'),
            'expire_minutes' => 60,
            'dashboard_url' => url('/user/dashboard'),
            'company_profile_url' => url('/employer/company/profile'),
            'hiring_services_url' => url('/employer/recruitment-requests'),
            'post_job_url' => url('/employer/new-job'),
            'profile_url' => url('/user/candidate/profile'),
            'cv_url' => url('/user/cv-manager'),
            'application_url' => url('/user/applied-jobs'),
            'completion_percent' => 65,
            'job_title' => 'Chief Officer — Container Vessel',
            'company_name' => 'Maersk Line',
            'interview_date' => 'Tuesday 5 May 2026, 14:00 GMT',
            'interview_location' => 'Microsoft Teams (link to follow)',
            'message' => 'Please bring your STCW certificate and a recent photo ID.',
            'contact_id' => 42,
            'phone' => '+234 902 430 4210',
            'subject' => 'Training & Certification',
            'category' => 'Training & Certification',
            'admin_url' => url('/admin/contacts/42'),
            'reply_url' => url('/contact/thread/sample-token'),
            'response' => 'Thanks for reaching out. Our training desk can help with the next available certification pathway.',
            'reply_message' => 'Thank you. Please send the next available training dates.',
            'candidate_name' => 'James Wilson',
            'chat_url' => url('/user/chat?conversation=1'),
            'note' => 'Please confirm your availability in the chat thread.',
            'documents' => 'Updated CV, STCW certificate, medical certificate, and passport bio page.',
            'offer_title' => 'Chief Officer conditional offer',
            'offer_details' => 'This offer is subject to document verification and final mobilization approval.',
            'year' => date('Y'),
        ];
    }
}
