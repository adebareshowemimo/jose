<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class PaymentEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'payment.confirmed',
                'name' => 'Payment Confirmed',
                'category' => 'Payment',
                'subject' => 'Payment confirmed for order {{order_number}}',
                'variables' => ['name', 'order_number', 'amount', 'currency', 'gateway', 'paid_at', 'order_url'],
                'body_html' => <<<'HTML'
<h2>Payment confirmed ✅</h2>
<p>Hi {{name}}, we've received and confirmed your payment for order <strong>{{order_number}}</strong>.</p>
<p style="font-size:24px; font-weight:700; color:#073057; margin:20px 0;">{{currency}} {{amount}}</p>
<ul>
  <li><strong>Order:</strong> {{order_number}}</li>
  <li><strong>Method:</strong> {{gateway}}</li>
  <li><strong>Confirmed:</strong> {{paid_at}}</li>
</ul>
<div class="btn-wrap"><a href="{{order_url}}" class="btn">View order &amp; receipt</a></div>
<p style="color:#6B7280; font-size:13px;">A receipt is available from your dashboard at any time. If you have any questions about this payment, just reply to this email.</p>
HTML,
            ],
            [
                'key' => 'payment.received',
                'name' => 'Bank Transfer Received — Awaiting Verification',
                'category' => 'Payment',
                'subject' => 'We received your transfer for order {{order_number}}',
                'variables' => ['name', 'order_number', 'amount', 'currency', 'transaction_id', 'order_url'],
                'body_html' => <<<'HTML'
<h2>Transfer details received</h2>
<p>Hi {{name}}, thanks — we've received your bank-transfer details for order <strong>{{order_number}}</strong>.</p>
<ul>
  <li><strong>Amount:</strong> {{currency}} {{amount}}</li>
  <li><strong>Your reference:</strong> {{transaction_id}}</li>
</ul>
<p>Our team will verify the transfer with our bank and confirm the payment within 1 business day. You'll receive a confirmation email as soon as it's verified.</p>
<div class="btn-wrap"><a href="{{order_url}}" class="btn">View order status</a></div>
<p style="color:#6B7280; font-size:13px;">If you don't hear back within 1 business day, please reply to this email and we'll investigate.</p>
HTML,
            ],
            [
                'key' => 'payment.admin_received',
                'name' => 'Bank Transfer Submitted (Admin)',
                'category' => 'Payment',
                'subject' => '[Verify] Bank transfer submitted for order {{order_number}}',
                'variables' => ['order_number', 'customer_name', 'customer_email', 'amount', 'currency', 'transaction_id', 'admin_url'],
                'body_html' => <<<'HTML'
<h2>New bank-transfer claim</h2>
<p><strong>{{customer_name}}</strong> ({{customer_email}}) has submitted bank-transfer details for order <strong>{{order_number}}</strong> and is awaiting verification.</p>
<ul>
  <li><strong>Amount:</strong> {{currency}} {{amount}}</li>
  <li><strong>Reference supplied by customer:</strong> {{transaction_id}}</li>
</ul>
<div class="btn-wrap"><a href="{{admin_url}}" class="btn">Open in admin</a></div>
HTML,
            ],
            [
                'key' => 'receipt.sent',
                'name' => 'Receipt Issued',
                'category' => 'Payment',
                'subject' => 'Your receipt for order {{order_number}} ({{receipt_number}})',
                'variables' => ['name', 'order_number', 'receipt_number', 'amount', 'currency', 'issued_at', 'download_url'],
                'body_html' => <<<'HTML'
<h2>Your receipt is ready</h2>
<p>Hi {{name}}, please find attached the receipt for your payment on order <strong>{{order_number}}</strong>.</p>
<ul>
  <li><strong>Receipt number:</strong> {{receipt_number}}</li>
  <li><strong>Amount:</strong> {{currency}} {{amount}}</li>
  <li><strong>Issued:</strong> {{issued_at}}</li>
</ul>
<div class="btn-wrap"><a href="{{download_url}}" class="btn">Download receipt</a></div>
<p style="color:#6B7280; font-size:13px;">You can also download this receipt anytime from your dashboard. If anything looks off, just reply to this email.</p>
HTML,
            ],
        ];

        foreach ($templates as $row) {
            EmailTemplate::updateOrCreate(['key' => $row['key']], $row + ['is_active' => true]);
        }
    }
}
