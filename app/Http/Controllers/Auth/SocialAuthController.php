<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const ALLOWED = ['google', 'microsoft'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, self::ALLOWED, true), 404);

        if (! config("services.{$provider}.client_id") || ! config("services.{$provider}.client_secret")) {
            return redirect()->route('auth.login')->withErrors([
                'email' => ucfirst($provider) . ' login is not configured. Please contact the site administrator.',
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        abort_unless(in_array($provider, self::ALLOWED, true), 404);

        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('auth.login')->withErrors([
                'email' => 'Sign-in with ' . ucfirst($provider) . ' failed. Please try again.',
            ]);
        }

        $email = $oauthUser->getEmail();
        if (! $email) {
            return redirect()->route('auth.login')->withErrors([
                'email' => 'Your ' . ucfirst($provider) . ' account did not provide an email address.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->forceFill([
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
                'provider_token' => $oauthUser->token,
                'email_verified_at' => $user->email_verified_at ?? now(),
                'is_verified' => true,
            ])->save();
        } else {
            $user = User::create([
                'name' => $oauthUser->getName() ?: $oauthUser->getNickname() ?: explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'role_id' => null,
                'status' => 'active',
                'email_verified_at' => now(),
                'is_verified' => true,
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
                'provider_token' => $oauthUser->token,
                'avatar' => $oauthUser->getAvatar(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if (! $user->role_id) {
            return redirect()->route('auth.complete-signup');
        }

        if ($user->role?->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role_id === 2) {
            return redirect()->route('employer.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
}
