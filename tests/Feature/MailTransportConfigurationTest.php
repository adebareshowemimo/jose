<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunHttpTransport;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridApiTransport;
use Tests\TestCase;

class MailTransportConfigurationTest extends TestCase
{
    public function test_mailgun_transport_can_be_selected(): void
    {
        config([
            'mail.mailers.mailgun' => ['transport' => 'mailgun'],
            'services.mailgun' => [
                'domain' => 'sandbox.example.mailgun.org',
                'secret' => 'test-mailgun-key',
                'scheme' => 'https',
                'endpoint' => 'default',
            ],
        ]);

        Mail::purge('mailgun');

        $transport = Mail::mailer('mailgun')->getSymfonyTransport();

        $this->assertInstanceOf(MailgunHttpTransport::class, $transport);
    }

    public function test_sendgrid_transport_remains_available(): void
    {
        config([
            'mail.mailers.sendgrid' => [
                'transport' => 'sendgrid',
                'api_key' => 'test-sendgrid-key',
            ],
        ]);

        Mail::purge('sendgrid');

        $transport = Mail::mailer('sendgrid')->getSymfonyTransport();

        $this->assertInstanceOf(SendgridApiTransport::class, $transport);
    }
}
