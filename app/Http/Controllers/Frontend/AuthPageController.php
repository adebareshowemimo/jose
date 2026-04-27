<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Auth;

class AuthPageController extends BasePageController
{
    public function login()
    {
        if (Auth::check()) {
            return $this->dashboardRedirect();
        }

        return view('pages.auth.login', [
            'pageTitle' => 'Login',
            'pageDescription' => 'Sign in to continue your hiring or career journey.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Login'],
            ],
        ]);
    }

    public function register()
    {
        if (Auth::check()) {
            return $this->dashboardRedirect();
        }

        return view('pages.auth.register', [
            'pageTitle' => 'Register',
            'pageDescription' => 'Create a candidate or employer account.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Register'],
            ],
            'accountTypes' => [
                ['value' => 'candidate', 'label' => 'Candidate Account'],
                ['value' => 'employer', 'label' => 'Employer Account'],
            ],
        ]);
    }

    public function forgotPassword()
    {
        if (Auth::check()) {
            return $this->dashboardRedirect();
        }

        return view('pages.auth.forgot-password', [
            'pageTitle' => 'Forgot Password',
            'pageDescription' => 'Enter your email to receive reset instructions.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Forgot Password'],
            ],
        ]);
    }

    protected function dashboardRedirect()
    {
        $user = Auth::user();
        if ($user->role?->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ((int) $user->role_id === 2) {
            return redirect()->route('employer.dashboard');
        }
        if (! $user->role_id) {
            return redirect()->route('auth.complete-signup');
        }
        return redirect()->route('user.dashboard');
    }

    public function resetPassword(string $token)
    {
        return view('pages.auth.reset-password', [
            'pageTitle' => 'Reset Password',
            'pageDescription' => 'Set a secure new password for your account.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Reset Password'],
            ],
            'token' => $token,
        ]);
    }

    public function verifyEmail()
    {
        return view('pages.auth.verify-email', [
            'pageTitle' => 'Email Verification',
            'pageDescription' => 'Please verify your email before accessing protected areas.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Email Verification'],
            ],
        ]);
    }
}
