<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\EmailDispatcher;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function notice(Request $request)
    {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return $this->redirectVerified($request->user());
        }

        return view('pages.auth.verify-email', [
            'pageTitle' => 'Verify your email',
            'pageDescription' => 'We sent a verification link to your inbox. Click the link to activate your account.',
        ]);
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectVerified($request->user());
        }

        if ($request->user()->markEmailAsVerified()) {
            $request->user()->forceFill(['is_verified' => true])->save();
            event(new Verified($request->user()));

            app(EmailDispatcher::class)->send($this->welcomeTemplateFor($request->user()), $request->user(), $this->welcomeVarsFor($request->user()));
        }

        return $this->redirectVerified($request->user());
    }

    protected function dashboardUrlFor($user): string
    {
        if ($user->role?->name === 'admin') return route('admin.dashboard');
        if ($user->role_id === 2) return route('employer.dashboard');
        if (! $user->role_id) return route('auth.complete-signup');
        return route('user.dashboard');
    }

    protected function welcomeTemplateFor($user): string
    {
        return $user->role?->name === 'employer' ? 'auth.welcome_employer' : 'auth.welcome';
    }

    protected function welcomeVarsFor($user): array
    {
        $vars = [
            'dashboard_url' => $this->dashboardUrlFor($user),
        ];

        if ($user->role?->name === 'employer') {
            $vars += [
                'company_name' => $user->company?->name ?? 'your company',
                'company_profile_url' => route('employer.company.profile'),
                'hiring_services_url' => route('employer.recruitment-requests.index'),
                'post_job_url' => route('employer.new-job'),
            ];
        }

        return $vars;
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectVerified($request->user());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    protected function redirectVerified($user)
    {
        if (! Auth::check()) {
            Auth::login($user);
        }

        if ($user->role?->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role_id === 2) {
            return redirect()->route('employer.dashboard');
        }
        if (! $user->role_id) {
            return redirect()->route('auth.complete-signup');
        }
        return redirect()->route('user.dashboard');
    }
}
