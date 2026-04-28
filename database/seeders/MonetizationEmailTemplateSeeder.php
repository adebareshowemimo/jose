<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class MonetizationEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'training.enrolment_confirmed',
                'name' => 'Training Enrolment Confirmed',
                'category' => 'Monetization',
                'subject' => "You're enrolled — {{program_title}}",
                'variables' => ['name', 'program_title', 'program_type', 'starts_at', 'duration', 'amount', 'currency', 'order_url'],
                'body_html' => <<<'HTML'
<h2>You're in, {{name}} 🎓</h2>
<p>Your enrolment in <strong>{{program_title}}</strong> ({{program_type}}) is confirmed.</p>
<ul>
  <li><strong>Starts:</strong> {{starts_at}}</li>
  <li><strong>Duration:</strong> {{duration}}</li>
  <li><strong>Paid:</strong> {{currency}} {{amount}}</li>
</ul>
<p>Our training desk will be in touch with joining instructions, materials, and any pre-course requirements.</p>
<div class="btn-wrap"><a href="{{order_url}}" class="btn">View enrolment & receipt</a></div>
HTML,
            ],
            [
                'key' => 'event.ticket_issued',
                'name' => 'Event Ticket Issued',
                'category' => 'Monetization',
                'subject' => "Your ticket — {{event_title}}",
                'variables' => ['name', 'event_title', 'event_date', 'event_location', 'ticket_count', 'total_amount', 'currency', 'registration_id'],
                'body_html' => <<<'HTML'
<h2>You're registered for {{event_title}} 🎟️</h2>
<p>Hi {{name}}, your registration is confirmed.</p>
<ul>
  <li><strong>When:</strong> {{event_date}}</li>
  <li><strong>Where:</strong> {{event_location}}</li>
  <li><strong>Tickets:</strong> {{ticket_count}}</li>
  <li><strong>Total paid:</strong> {{currency}} {{total_amount}}</li>
  <li><strong>Reference:</strong> #{{registration_id}}</li>
</ul>
<p>We'll send a reminder closer to the event date with venue details and any materials.</p>
HTML,
            ],
            [
                'key' => 'candidate.boost_activated',
                'name' => 'Candidate Boost Activated',
                'category' => 'Monetization',
                'subject' => "Your profile is now boosted — featured until {{ends_at}}",
                'variables' => ['name', 'days', 'ends_at', 'amount', 'currency', 'profile_url'],
                'body_html' => <<<'HTML'
<h2>Your profile is now featured ✨</h2>
<p>Hi {{name}}, your visibility boost is active for the next <strong>{{days}} days</strong>, until <strong>{{ends_at}}</strong>.</p>
<p>While featured, your profile appears at the top of our candidate listing — meaning more eyes from employers actively searching for talent.</p>
<ul>
  <li><strong>Paid:</strong> {{currency}} {{amount}}</li>
</ul>
<div class="btn-wrap"><a href="{{profile_url}}" class="btn">View my profile</a></div>
<p style="color:#6B7280; font-size:13px;">Tip: profiles with a complete photo, summary, and recent CV get the most replies during a boost.</p>
HTML,
            ],
            [
                'key' => 'subscription.started',
                'name' => 'Subscription Started',
                'category' => 'Monetization',
                'subject' => "Welcome to {{plan_name}}",
                'variables' => ['name', 'plan_name', 'billing_cycle', 'ends_at', 'amount', 'currency'],
                'body_html' => <<<'HTML'
<h2>Welcome to {{plan_name}}, {{name}} 🎉</h2>
<p>Your subscription is active. Here are the details:</p>
<ul>
  <li><strong>Plan:</strong> {{plan_name}}</li>
  <li><strong>Billing:</strong> {{billing_cycle}}</li>
  <li><strong>Renews:</strong> {{ends_at}}</li>
  <li><strong>Paid:</strong> {{currency}} {{amount}}</li>
</ul>
<p>You now have access to all the perks tied to this plan. We'll send a renewal reminder before the next billing date.</p>
HTML,
            ],
            [
                'key' => 'subscription.renewed',
                'name' => 'Subscription Renewed',
                'category' => 'Monetization',
                'subject' => "Your {{plan_name}} subscription has been renewed",
                'variables' => ['name', 'plan_name', 'billing_cycle', 'ends_at', 'amount', 'currency'],
                'body_html' => <<<'HTML'
<h2>Subscription renewed — thanks {{name}}</h2>
<p>Your <strong>{{plan_name}}</strong> ({{billing_cycle}}) subscription has been renewed.</p>
<ul>
  <li><strong>Paid:</strong> {{currency}} {{amount}}</li>
  <li><strong>Active until:</strong> {{ends_at}}</li>
</ul>
<p>No action needed — everything keeps running. Reply to this email if you'd like to make any changes.</p>
HTML,
            ],
        ];

        foreach ($templates as $row) {
            EmailTemplate::updateOrCreate(['key' => $row['key']], $row + ['is_active' => true]);
        }
    }
}
