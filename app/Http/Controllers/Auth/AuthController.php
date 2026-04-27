<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is blocked
            if ($user->status === 'blocked') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been blocked. Please contact support.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();

            // Redirect based on user role
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:candidate,employer',
            'terms' => 'accepted',
        ];

        $messages = [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Passwords do not match',
            'role.required' => 'Please select an account type',
            'terms.accepted' => 'You must accept the terms and conditions',
            'company_name.required_if' => 'Company name is required for employer accounts',
        ];

        if ($request->role === 'employer') {
            $rules['company_name'] = 'required_if:role,employer|string|max:255';
        }

        $request->validate($rules, $messages);

        // Determine role_id by looking up the role name (avoids hardcoded IDs)
        $role = Role::where('name', $request->role)->first();
        if (! $role) {
            return back()->withErrors(['role' => 'Invalid account type selected.'])->withInput();
        }

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        // Create company profile for employer accounts
        if ($request->role === 'employer') {
            $user->company()->create([
                'name' => $request->company_name,
                'slug' => \Illuminate\Support\Str::slug($request->company_name) . '-' . $user->id,
                'email' => $request->email,
                'status' => 'active',
            ]);
        }

        $employerVars = [];
        if ($request->role === 'employer') {
            $employerVars = [
                'company_name' => $user->company?->name ?? $request->company_name,
                'company_profile_url' => route('employer.company.profile'),
                'hiring_services_url' => route('employer.recruitment-requests.index'),
                'post_job_url' => route('employer.new-job'),
            ];
        }

        // Always send a registration confirmation receipt.
        app(EmailDispatcher::class)->send($this->registrationTemplateFor($user), $user, [
            'login_url' => route('auth.login'),
        ] + $employerVars);

        if (setting('auth.require_email_verification', false)) {
            $user->sendEmailVerificationNotification();
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('auth.verify-email');
        }

        $welcomeVars = [
            'dashboard_url' => $request->role === 'employer' ? route('employer.dashboard') : route('user.dashboard'),
        ];

        $welcomeVars += $employerVars;

        // No verification required: send the welcome email immediately.
        app(EmailDispatcher::class)->send($this->welcomeTemplateFor($user), $user, $welcomeVars);

        Auth::login($user);

        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->role?->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role_id === 2) {
            return redirect()->route('employer.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    protected function welcomeTemplateFor(User $user): string
    {
        return $user->role?->name === 'employer' ? 'auth.welcome_employer' : 'auth.welcome';
    }

    protected function registrationTemplateFor(User $user): string
    {
        return $user->role?->name === 'employer' ? 'auth.registration_confirmation_employer' : 'auth.registration_confirmation';
    }
}
