<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

/**
 * Spam protection for public, unauthenticated forms.
 *
 * Works together with the <x-honeypot /> Blade component, which injects two
 * fields into the form:
 *   - a visually-hidden "website" trap that humans never see (so it stays
 *     empty) but automated bots tend to fill in, and
 *   - an encrypted timestamp recording when the form was rendered.
 *
 * A request is treated as a bot when the trap is filled, or when the form is
 * submitted faster than a human plausibly could. Detected bots get a fake
 * success response so they gain no signal that they were blocked.
 */
class Honeypot
{
    /** Hidden field that must stay empty; bots fill it. */
    private const TRAP_FIELD = 'website';

    /** Encrypted render-time timestamp field. */
    private const TIME_FIELD = '_hpt';

    /** Minimum seconds a genuine human takes to complete a form. */
    private const MIN_SECONDS = 2;

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->looksLikeBot($request)) {
            // Pretend it succeeded — never tell the bot it was caught.
            $message = 'Thank you. Your submission has been received.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message]);
            }

            return back()->with('success', $message);
        }

        return $next($request);
    }

    private function looksLikeBot(Request $request): bool
    {
        // 1) The trap field must be empty.
        if (filled($request->input(self::TRAP_FIELD))) {
            return true;
        }

        // 2) The form must carry a valid, not-too-fresh timestamp.
        $token = $request->input(self::TIME_FIELD);

        if (! is_string($token) || $token === '') {
            return true;
        }

        try {
            $renderedAt = (int) Crypt::decryptString($token);
        } catch (DecryptException) {
            return true;
        }

        return (time() - $renderedAt) < self::MIN_SECONDS;
    }
}
