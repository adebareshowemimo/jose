<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── Auth ──────────────────────────────────────────────────
            [
                'key' => 'auth.registration_confirmation',
                'name' => 'Candidate Registration Confirmation',
                'category' => 'Authentication',
                'subject' => 'Thanks for signing up to {{app_name}}, {{name}}',
                'variables' => ['name', 'app_name', 'app_url', 'login_url'],
                'body_html' => <<<'HTML'
<h2>Hi {{name}}, your account is created.</h2>
<p>Thanks for signing up to <strong>{{app_name}}</strong>. Here's a quick look at what you can do next:</p>
<ul>
  <li><strong>Complete your profile</strong> so employers can find you.</li>
  <li><strong>Upload your CV</strong> to apply to roles in a single click.</li>
  <li><strong>Browse open jobs</strong> across maritime, logistics, and energy.</li>
</ul>
<div class="btn-wrap"><a href="{{login_url}}" class="btn">Sign in to my account</a></div>
<p style="color:#6B7280; font-size:13px;">If you didn't create this account, please <a href="mailto:{{support_email}}">contact our team</a> right away.</p>
HTML,
            ],
            [
                'key' => 'auth.registration_confirmation_employer',
                'name' => 'Employer Registration Confirmation',
                'category' => 'Authentication',
                'subject' => 'Thanks for registering {{company_name}} on {{app_name}}',
                'variables' => ['name', 'company_name', 'app_name', 'app_url', 'login_url'],
                'body_html' => <<<'HTML'
<h2>Hi {{name}}, your employer account is created.</h2>
<p>Thanks for registering <strong>{{company_name}}</strong> on <strong>{{app_name}}</strong>.</p>
<p>Once your account is active, you can manage your company profile, post jobs, and request matched candidates through Hiring Services.</p>
<div class="btn-wrap"><a href="{{login_url}}" class="btn">Sign in to employer account</a></div>
<p style="color:#6B7280; font-size:13px;">If you didn't create this employer account, please <a href="mailto:{{support_email}}">contact our team</a> right away.</p>
HTML,
            ],
            [
                'key' => 'auth.verify_email',
                'name' => 'Email Verification',
                'category' => 'Authentication',
                'subject' => 'Confirm your email for {{app_name}}',
                'variables' => ['name', 'verify_url', 'app_name', 'app_url'],
                'body_html' => <<<'HTML'
<h2>Hi {{name}}, welcome aboard!</h2>
<p>Thanks for signing up to <strong>{{app_name}}</strong>. To finish setting up your account, please confirm your email address by clicking the button below:</p>
<div class="btn-wrap"><a href="{{verify_url}}" class="btn">Verify my email</a></div>
<p style="color:#6B7280; font-size:13px;">If the button doesn't work, copy and paste this link into your browser:<br><a href="{{verify_url}}">{{verify_url}}</a></p>
<p style="color:#6B7280; font-size:13px;">This link will expire in 60 minutes for your security.</p>
HTML,
            ],
            [
                'key' => 'auth.welcome',
                'name' => 'Candidate Welcome Email',
                'category' => 'Authentication',
                'subject' => 'Welcome to {{app_name}}, {{name}}!',
                'variables' => ['name', 'dashboard_url', 'app_name'],
                'body_html' => <<<'HTML'
<h2>You're in, {{name}} 🎉</h2>
<p>Your email is verified and your <strong>{{app_name}}</strong> account is ready. Here's what you can do next:</p>
<ul>
  <li>Complete your profile so employers can find you</li>
  <li>Upload your CV to apply faster to roles</li>
  <li>Browse open maritime, logistics, and energy jobs</li>
</ul>
<div class="btn-wrap"><a href="{{dashboard_url}}" class="btn">Go to my dashboard</a></div>
<p>If you ever need help, just reply to this email — our team reads every message.</p>
HTML,
            ],
            [
                'key' => 'auth.welcome_employer',
                'name' => 'Employer Welcome Email',
                'category' => 'Authentication',
                'subject' => 'Welcome to {{app_name}}',
                'variables' => ['name', 'company_name', 'dashboard_url', 'company_profile_url', 'hiring_services_url', 'post_job_url', 'app_name'],
                'body_html' => <<<'HTML'
<h2>Welcome, {{name}}</h2>
<p>Your employer account for <strong>{{company_name}}</strong> is verified and ready on <strong>{{app_name}}</strong>.</p>
<p>Here are the best next steps for your company account:</p>
<ul>
  <li>Complete your company profile so candidates can understand your organization</li>
  <li>Post a job when you are ready to receive applications</li>
  <li>Use Hiring Services when you want our admin team to source and deliver matched candidates</li>
  <li>Review assigned candidates and continue the hiring workflow from your employer dashboard</li>
</ul>
<div class="btn-wrap"><a href="{{dashboard_url}}" class="btn">Go to employer dashboard</a></div>
<p style="color:#6B7280; font-size:13px;">
  Company profile: <a href="{{company_profile_url}}">{{company_profile_url}}</a><br>
  Hiring services: <a href="{{hiring_services_url}}">{{hiring_services_url}}</a><br>
  Post a job: <a href="{{post_job_url}}">{{post_job_url}}</a>
</p>
<p>If you ever need help, just reply to this email — our team reads every message.</p>
HTML,
            ],
            [
                'key' => 'auth.reset_password',
                'name' => 'Password Reset',
                'category' => 'Authentication',
                'subject' => 'Reset your {{app_name}} password',
                'variables' => ['name', 'reset_url', 'expire_minutes', 'app_name'],
                'body_html' => <<<'HTML'
<h2>Reset your password</h2>
<p>Hi {{name}}, we got a request to reset the password on your <strong>{{app_name}}</strong> account.</p>
<div class="btn-wrap"><a href="{{reset_url}}" class="btn">Reset my password</a></div>
<p style="color:#6B7280; font-size:13px;">This link will expire in {{expire_minutes}} minutes.</p>
<p style="color:#6B7280; font-size:13px;">If you didn't request this, you can safely ignore this email — your password won't change.</p>
HTML,
            ],

            // ── Reminders ─────────────────────────────────────────────
            [
                'key' => 'reminder.profile_completion',
                'name' => 'Pending Profile Completion',
                'category' => 'Reminder',
                'subject' => 'Finish your profile to unlock more opportunities',
                'variables' => ['name', 'completion_percent', 'profile_url', 'app_name'],
                'body_html' => <<<'HTML'
<h2>Your profile is {{completion_percent}}% complete</h2>
<p>Hi {{name}}, employers on {{app_name}} are <strong>3× more likely</strong> to contact candidates with a complete profile.</p>
<p>You're almost there — adding a few more details takes just a couple of minutes:</p>
<ul>
  <li>Professional title and bio</li>
  <li>Work experience and education</li>
  <li>Skills and certifications</li>
</ul>
<div class="btn-wrap"><a href="{{profile_url}}" class="btn">Complete my profile</a></div>
HTML,
            ],
            [
                'key' => 'reminder.cv_upload',
                'name' => 'CV Upload Reminder',
                'category' => 'Reminder',
                'subject' => 'Upload your CV to apply faster',
                'variables' => ['name', 'cv_url', 'app_name'],
                'body_html' => <<<'HTML'
<h2>Got 2 minutes?</h2>
<p>Hi {{name}}, you haven't uploaded your CV yet. With your CV on file, you can apply to any role on {{app_name}} in a single click.</p>
<div class="btn-wrap"><a href="{{cv_url}}" class="btn">Upload my CV</a></div>
<p style="color:#6B7280; font-size:13px;">PDF or DOCX, up to 5 MB. You can replace it anytime.</p>
HTML,
            ],

            // ── Job Application Stages ────────────────────────────────
            [
                'key' => 'application.received',
                'name' => 'Application Received',
                'category' => 'Job Notification',
                'subject' => 'Application received: {{job_title}}',
                'variables' => ['name', 'job_title', 'company_name', 'application_url'],
                'body_html' => <<<'HTML'
<h2>Application received ✅</h2>
<p>Hi {{name}}, <strong>{{company_name}}</strong> has received your application for the <strong>{{job_title}}</strong> role.</p>
<p>The hiring team will review your profile and reach out if they'd like to take the next step. You can track this application from your dashboard.</p>
<div class="btn-wrap"><a href="{{application_url}}" class="btn">View my applications</a></div>
HTML,
            ],
            [
                'key' => 'application.shortlisted',
                'name' => 'Application Shortlisted',
                'category' => 'Job Notification',
                'subject' => 'You\'ve been shortlisted for {{job_title}}',
                'variables' => ['name', 'job_title', 'company_name', 'application_url', 'message'],
                'body_html' => <<<'HTML'
<h2>Great news — you're shortlisted!</h2>
<p>Hi {{name}}, congratulations — your application for <strong>{{job_title}}</strong> at <strong>{{company_name}}</strong> has been shortlisted.</p>
<p>{{message}}</p>
<div class="btn-wrap"><a href="{{application_url}}" class="btn">View details</a></div>
HTML,
            ],
            [
                'key' => 'application.interview',
                'name' => 'Interview Invitation',
                'category' => 'Job Notification',
                'subject' => 'Interview invitation: {{job_title}}',
                'variables' => ['name', 'job_title', 'company_name', 'interview_date', 'interview_location', 'message'],
                'body_html' => <<<'HTML'
<h2>You're invited to interview</h2>
<p>Hi {{name}}, <strong>{{company_name}}</strong> would like to interview you for the <strong>{{job_title}}</strong> role.</p>
<p><strong>When:</strong> {{interview_date}}<br>
<strong>Where:</strong> {{interview_location}}</p>
<p>{{message}}</p>
<p>Please reply to this email or contact the employer to confirm your attendance.</p>
HTML,
            ],
            [
                'key' => 'application.offered',
                'name' => 'Job Offer',
                'category' => 'Job Notification',
                'subject' => 'You have a job offer from {{company_name}}',
                'variables' => ['name', 'job_title', 'company_name', 'application_url', 'message'],
                'body_html' => <<<'HTML'
<h2>🎉 You've been offered the role!</h2>
<p>Hi {{name}}, <strong>{{company_name}}</strong> would like to extend you an offer for the <strong>{{job_title}}</strong> position.</p>
<p>{{message}}</p>
<div class="btn-wrap"><a href="{{application_url}}" class="btn">Review the offer</a></div>
HTML,
            ],
            [
                'key' => 'application.hired',
                'name' => 'Hired Confirmation',
                'category' => 'Job Notification',
                'subject' => 'Welcome to the team — {{company_name}}',
                'variables' => ['name', 'job_title', 'company_name', 'message'],
                'body_html' => <<<'HTML'
<h2>Welcome aboard, {{name}}!</h2>
<p>Congratulations — you're officially joining <strong>{{company_name}}</strong> as a <strong>{{job_title}}</strong>.</p>
<p>{{message}}</p>
<p>The hiring team will be in touch with onboarding details and next steps.</p>
HTML,
            ],
            [
                'key' => 'application.rejected',
                'name' => 'Application Not Selected',
                'category' => 'Job Notification',
                'subject' => 'Update on your {{job_title}} application',
                'variables' => ['name', 'job_title', 'company_name', 'message'],
                'body_html' => <<<'HTML'
<h2>Update on your application</h2>
<p>Hi {{name}}, thank you for applying to the <strong>{{job_title}}</strong> role at <strong>{{company_name}}</strong>.</p>
<p>After careful consideration, the hiring team has decided to move forward with other candidates for this position.</p>
<p>{{message}}</p>
<p>Don't be discouraged — there are always new opportunities being posted on {{app_name}}. We wish you the very best.</p>
HTML,
            ],

            // ── Contact Form ─────────────────────────────────────────
            [
                'key' => 'contact.auto_response',
                'name' => 'Contact Form Auto Response',
                'category' => 'Contact',
                'subject' => 'We received your message: {{subject}}',
                'variables' => ['name', 'email', 'phone', 'subject', 'category', 'message', 'reply_url', 'app_name'],
                'body_html' => <<<'HTML'
<h2>Hi {{name}}, thanks for contacting us.</h2>
<p>We received your message about <strong>{{subject}}</strong>. A member of the JCL team will review it and respond as soon as possible.</p>
<p><strong>Your message:</strong></p>
<p>{{message}}</p>
<div class="btn-wrap"><a href="{{reply_url}}" class="btn">View this conversation</a></div>
<p style="color:#6B7280; font-size:13px;">You can use the conversation link above to send follow-up information.</p>
HTML,
            ],
            [
                'key' => 'contact.admin_notification',
                'name' => 'New Contact Admin Notification',
                'category' => 'Contact',
                'subject' => 'New contact form submission from {{name}}',
                'variables' => ['contact_id', 'name', 'email', 'phone', 'subject', 'category', 'message', 'admin_url'],
                'body_html' => <<<'HTML'
<h2>New contact submission #{{contact_id}}</h2>
<p><strong>Name:</strong> {{name}}<br>
<strong>Email:</strong> {{email}}<br>
<strong>Phone:</strong> {{phone}}<br>
<strong>Category:</strong> {{category}}<br>
<strong>Subject:</strong> {{subject}}</p>
<p><strong>Message:</strong></p>
<p>{{message}}</p>
<div class="btn-wrap"><a href="{{admin_url}}" class="btn">Open in admin</a></div>
HTML,
            ],
            [
                'key' => 'contact.admin_reply',
                'name' => 'Admin Reply to Contact',
                'category' => 'Contact',
                'subject' => 'Response from JCL: {{subject}}',
                'variables' => ['name', 'subject', 'message', 'response', 'reply_url'],
                'body_html' => <<<'HTML'
<h2>Hi {{name}},</h2>
<p>Our team has responded to your enquiry: <strong>{{subject}}</strong>.</p>
<p><strong>Response:</strong></p>
<p>{{response}}</p>
<div class="btn-wrap"><a href="{{reply_url}}" class="btn">Reply to this message</a></div>
<p style="color:#6B7280; font-size:13px;">Your original message: {{message}}</p>
HTML,
            ],
            [
                'key' => 'contact.user_reply_notification',
                'name' => 'Contact User Reply Notification',
                'category' => 'Contact',
                'subject' => '{{name}} replied to contact submission #{{contact_id}}',
                'variables' => ['contact_id', 'name', 'email', 'subject', 'reply_message', 'admin_url'],
                'body_html' => <<<'HTML'
<h2>New reply on contact submission #{{contact_id}}</h2>
<p><strong>Name:</strong> {{name}}<br>
<strong>Email:</strong> {{email}}<br>
<strong>Subject:</strong> {{subject}}</p>
<p><strong>Reply:</strong></p>
<p>{{reply_message}}</p>
<div class="btn-wrap"><a href="{{admin_url}}" class="btn">Open in admin</a></div>
HTML,
            ],

            // ── Chat Actions ─────────────────────────────────────────
            [
                'key' => 'chat.interview_scheduled',
                'name' => 'Chat Interview Scheduled',
                'category' => 'Chat',
                'subject' => 'Interview scheduled for {{job_title}}',
                'variables' => ['name', 'company_name', 'job_title', 'interview_date', 'interview_location', 'note', 'chat_url'],
                'body_html' => <<<'HTML'
<h2>Interview scheduled</h2>
<p>Hi {{name}}, <strong>{{company_name}}</strong> has scheduled an interview for <strong>{{job_title}}</strong>.</p>
<p><strong>When:</strong> {{interview_date}}<br>
<strong>Where:</strong> {{interview_location}}</p>
<p>{{note}}</p>
<div class="btn-wrap"><a href="{{chat_url}}" class="btn">Open chat</a></div>
HTML,
            ],
            [
                'key' => 'chat.documents_requested',
                'name' => 'Chat Documents Requested',
                'category' => 'Chat',
                'subject' => '{{company_name}} requested documents',
                'variables' => ['name', 'company_name', 'job_title', 'documents', 'note', 'chat_url'],
                'body_html' => <<<'HTML'
<h2>Documents requested</h2>
<p>Hi {{name}}, <strong>{{company_name}}</strong> requested documents for <strong>{{job_title}}</strong>.</p>
<p><strong>Requested documents:</strong></p>
<p>{{documents}}</p>
<p>{{note}}</p>
<div class="btn-wrap"><a href="{{chat_url}}" class="btn">Open chat</a></div>
HTML,
            ],
            [
                'key' => 'chat.offer_sent',
                'name' => 'Chat Offer Sent',
                'category' => 'Chat',
                'subject' => 'Offer from {{company_name}}: {{offer_title}}',
                'variables' => ['name', 'company_name', 'job_title', 'offer_title', 'offer_details', 'chat_url'],
                'body_html' => <<<'HTML'
<h2>You have an offer message</h2>
<p>Hi {{name}}, <strong>{{company_name}}</strong> sent an offer message for <strong>{{job_title}}</strong>.</p>
<p><strong>{{offer_title}}</strong></p>
<p>{{offer_details}}</p>
<div class="btn-wrap"><a href="{{chat_url}}" class="btn">Review in chat</a></div>
HTML,
            ],
        ];

        foreach ($templates as $row) {
            EmailTemplate::updateOrCreate(
                ['key' => $row['key']],
                $row + ['is_active' => true]
            );
        }
    }
}
