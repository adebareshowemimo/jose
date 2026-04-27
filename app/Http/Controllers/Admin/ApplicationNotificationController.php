<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\JobApplication;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class ApplicationNotificationController extends Controller
{
    public function send(Request $request, EmailDispatcher $dispatcher)
    {
        $data = $request->validate([
            'application_ids'   => 'required|array|min:1',
            'application_ids.*' => 'integer|exists:job_applications,id',
            'template_key'      => 'required|string|exists:email_templates,key',
            'message'           => 'nullable|string|max:5000',
            'interview_date'    => 'nullable|string|max:255',
            'interview_location'=> 'nullable|string|max:255',
            'update_status'     => 'nullable|string|in:reviewed,shortlisted,interviewed,offered,hired,rejected',
        ]);

        $template = EmailTemplate::findByKey($data['template_key']);
        if (! $template || ! str_starts_with($template->category, 'Job')) {
            return back()->with('error', 'Selected template is not a job-notification template.');
        }

        $apps = JobApplication::with(['candidate.user', 'jobListing.company'])
            ->whereIn('id', $data['application_ids'])
            ->get();

        $sent = 0;
        $failed = 0;
        foreach ($apps as $app) {
            $user = $app->candidate?->user;
            if (! $user || ! $user->email) {
                $failed++;
                continue;
            }

            $vars = [
                'job_title'         => $app->jobListing?->title ?? '',
                'company_name'      => $app->jobListing?->company?->name ?? config('app.name'),
                'application_url'   => url('/user/applied-jobs'),
                'message'           => $data['message'] ?? '',
                'interview_date'    => $data['interview_date'] ?? '',
                'interview_location'=> $data['interview_location'] ?? '',
            ];

            $ok = $dispatcher->send($data['template_key'], $user, $vars);
            $ok ? $sent++ : $failed++;

            if ($ok && ! empty($data['update_status'])) {
                $app->status = $data['update_status'];
                $app->save();
            }
        }

        $msg = "Sent {$sent} email(s).";
        if ($failed > 0) $msg .= " {$failed} failed.";
        return back()->with($failed === 0 ? 'success' : 'warning', $msg);
    }
}
