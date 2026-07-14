<?php

namespace App\Providers;

use App\Models\Candidate;
use App\Models\Event;
use App\Models\Order;
use App\Models\Plan;
use App\Models\RecruitmentRequest;
use App\Models\TrainingProgram;
use App\Observers\OrderObserver;
use App\Support\OrderFulfillment\HandlerRegistry;
use App\Support\OrderFulfillment\Handlers\CandidateHandler;
use App\Support\OrderFulfillment\Handlers\EventHandler;
use App\Support\OrderFulfillment\Handlers\PlanHandler;
use App\Support\OrderFulfillment\Handlers\RecruitmentRequestHandler;
use App\Support\OrderFulfillment\Handlers\TrainingProgramHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridApiTransport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HandlerRegistry::class, function () {
            $registry = new HandlerRegistry();
            $registry->register(RecruitmentRequest::class, RecruitmentRequestHandler::class);
            $registry->register(TrainingProgram::class, TrainingProgramHandler::class);
            $registry->register(Event::class, EventHandler::class);
            $registry->register(Candidate::class, CandidateHandler::class);
            $registry->register(Plan::class, PlanHandler::class);
            return $registry;
        });
    }

    public function boot(): void
    {
        View::addNamespace('Layout', base_path('modules/Layout'));
        Order::observe(OrderObserver::class);

        // SendGrid Web API transport (HTTPS to api.sendgrid.com), as an alternative to
        // SMTP relay. Avoids blocked SMTP ports and surfaces real JSON errors from
        // SendGrid instead of opaque codes. Handles attachments/reply-to via the
        // official Symfony bridge, which our ticket + receipt PDFs rely on.
        Mail::extend('sendgrid', function (array $config) {
            $key = trim((string) ($config['api_key'] ?? ''));

            if ($key === '') {
                throw new \RuntimeException(
                    'SendGrid API key is not configured. Set it in Admin → Settings → Email, or SENDGRID_API_KEY in .env.'
                );
            }

            return new SendgridApiTransport($key);
        });
    }
}
