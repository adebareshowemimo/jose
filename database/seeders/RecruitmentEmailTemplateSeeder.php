<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class RecruitmentEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'recruitment.request_received',
                'name' => 'Recruitment Request Received',
                'category' => 'Recruitment',
                'subject' => 'We received your recruitment request, {{name}}',
                'variables' => ['name', 'company_name', 'service_type', 'cv_count', 'job_title', 'request_url'],
                'body_html' => <<<'HTML'
<h2>Thanks, {{name}} — your request is in.</h2>
<p>We've received your <strong>{{service_type}}</strong> request for <strong>{{cv_count}}</strong> candidate(s) for the <strong>{{job_title}}</strong> role at <strong>{{company_name}}</strong>.</p>
<p>Our team will review the brief and get back to you within one business day with a quote and timeline.</p>
<div class="btn-wrap"><a href="{{request_url}}" class="btn">View my request</a></div>
HTML,
            ],
            [
                'key' => 'recruitment.admin_new_request',
                'name' => 'New Recruitment Request (Admin)',
                'category' => 'Recruitment',
                'subject' => '[New Request] {{company_name}} — {{service_type}} for {{job_title}}',
                'variables' => ['company_name', 'service_type', 'cv_count', 'job_title', 'requester_name', 'requester_email', 'admin_url'],
                'body_html' => <<<'HTML'
<h2>New recruitment request</h2>
<p><strong>{{company_name}}</strong> just submitted a recruitment request.</p>
<ul>
  <li><strong>Service:</strong> {{service_type}}</li>
  <li><strong>CVs requested:</strong> {{cv_count}}</li>
  <li><strong>Role:</strong> {{job_title}}</li>
  <li><strong>Requested by:</strong> {{requester_name}} ({{requester_email}})</li>
</ul>
<div class="btn-wrap"><a href="{{admin_url}}" class="btn">Open in admin</a></div>
HTML,
            ],
            [
                'key' => 'recruitment.quote_sent',
                'name' => 'Recruitment Quote Sent',
                'category' => 'Recruitment',
                'subject' => 'Your recruitment quote is ready — {{job_title}}',
                'variables' => ['name', 'job_title', 'quoted_amount', 'currency', 'quote_note', 'order_url'],
                'body_html' => <<<'HTML'
<h2>Your quote is ready</h2>
<p>Hi {{name}}, we've reviewed your recruitment request for <strong>{{job_title}}</strong> and prepared a quote.</p>
<p style="font-size:24px; font-weight:700; color:#073057; margin: 20px 0;">{{currency}} {{quoted_amount}}</p>
<p>{{quote_note}}</p>
<div class="btn-wrap"><a href="{{order_url}}" class="btn">View invoice &amp; pay</a></div>
<p style="color:#6B7280; font-size:13px;">Questions about the scope or pricing? Just reply to this email.</p>
HTML,
            ],
            [
                'key' => 'recruitment.payment_confirmed',
                'name' => 'Recruitment Payment Confirmed',
                'category' => 'Recruitment',
                'subject' => 'Payment received — we\'re sourcing candidates for {{job_title}}',
                'variables' => ['name', 'job_title', 'amount', 'currency', 'paid_at', 'request_url'],
                'body_html' => <<<'HTML'
<h2>Payment confirmed — we're on it</h2>
<p>Hi {{name}}, we've received your payment of <strong>{{currency}} {{amount}}</strong> on {{paid_at}} for the <strong>{{job_title}}</strong> recruitment request.</p>
<p>Our team is now sourcing and screening candidates for the role. You'll get an email as soon as they're ready for you to review.</p>
<div class="btn-wrap"><a href="{{request_url}}" class="btn">View request</a></div>
<p style="color:#6B7280; font-size:13px;">Questions? Just reply to this email.</p>
HTML,
            ],
            [
                'key' => 'recruitment.candidates_delivered',
                'name' => 'Candidates Delivered',
                'category' => 'Recruitment',
                'subject' => 'Your candidates are ready — {{job_title}}',
                'variables' => ['name', 'job_title', 'candidate_count', 'request_url'],
                'body_html' => <<<'HTML'
<h2>{{candidate_count}} candidate(s) ready for review</h2>
<p>Hi {{name}}, we've sourced and screened candidates for your <strong>{{job_title}}</strong> role. They're now available for you to review.</p>
<div class="btn-wrap"><a href="{{request_url}}" class="btn">Review candidates</a></div>
<p>You can mark each candidate as shortlisted or rejected — and we'll move shortlisted ones forward together.</p>
HTML,
            ],
            [
                'key' => 'recruitment.completed',
                'name' => 'Recruitment Request Completed',
                'category' => 'Recruitment',
                'subject' => 'Recruitment request closed — {{job_title}}',
                'variables' => ['name', 'job_title', 'request_url'],
                'body_html' => <<<'HTML'
<h2>Wrapping up your request</h2>
<p>Hi {{name}}, your recruitment request for <strong>{{job_title}}</strong> has been marked as completed. Thanks for working with us.</p>
<div class="btn-wrap"><a href="{{request_url}}" class="btn">View final summary</a></div>
<p>Need to hire again? Submit another request from your dashboard any time.</p>
HTML,
            ],
        ];

        foreach ($templates as $row) {
            EmailTemplate::updateOrCreate(['key' => $row['key']], $row + ['is_active' => true]);
        }
    }
}
