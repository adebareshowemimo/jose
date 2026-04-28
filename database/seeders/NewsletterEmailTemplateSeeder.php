<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class NewsletterEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'newsletter.welcome',
                'name' => 'Newsletter — Welcome / Confirmation',
                'category' => 'Newsletter',
                'subject' => 'Welcome to the {{app_name}} newsletter, {{name}}',
                'variables' => ['name', 'app_name', 'unsubscribe_url'],
                'body_html' => <<<'HTML'
<h2>Welcome aboard, {{name}}!</h2>
<p>Thanks for subscribing to the <strong>{{app_name}}</strong> newsletter. Once a month, we'll send you a roundup of:</p>
<ul>
  <li>The latest maritime, offshore and energy hiring trends</li>
  <li>Certification and compliance updates that affect your role</li>
  <li>Featured roles from employers across our platform</li>
  <li>Editorial insights from our recruitment team</li>
</ul>
<p>No spam — promise. If we ever stop being useful, you can unsubscribe with one click using the link below.</p>
<p style="margin-top:32px; color:#6B7280; font-size:12px;">
  Don't want these emails any more? <a href="{{unsubscribe_url}}" style="color:#1AAD94;">Unsubscribe here</a>.
</p>
HTML,
            ],
            [
                'key' => 'newsletter.unsubscribed',
                'name' => 'Newsletter — Unsubscribe Confirmation',
                'category' => 'Newsletter',
                'subject' => "You've been unsubscribed from {{app_name}}",
                'variables' => ['name', 'app_name', 'resubscribe_url'],
                'body_html' => <<<'HTML'
<h2>You've been unsubscribed</h2>
<p>Hi {{name}}, this is just a quick confirmation that we've removed your email from the <strong>{{app_name}}</strong> newsletter.</p>
<p>You won't receive any more newsletters from us — but you'll still get any transactional emails (like account activity or order receipts).</p>
<p>If this was a mistake, you can always rejoin from any article on our news page:</p>
<div class="btn-wrap"><a href="{{resubscribe_url}}" class="btn">Browse our news</a></div>
<p style="color:#6B7280; font-size:13px;">Sorry to see you go. If there's something we could have done better, just reply to this email — we read every response.</p>
HTML,
            ],
        ];

        foreach ($templates as $row) {
            EmailTemplate::updateOrCreate(['key' => $row['key']], $row + ['is_active' => true]);
        }
    }
}
