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
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
    }
}
