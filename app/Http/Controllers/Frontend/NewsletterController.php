<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request, EmailDispatcher $dispatcher)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => strtolower($data['email'])]);

        $alreadyActive = $subscriber->exists && $subscriber->isActive();

        $subscriber->fill([
            'name' => $data['name'] ?? $subscriber->name,
            'source' => $data['source'] ?? $subscriber->source ?? 'newsletter_form',
            'status' => NewsletterSubscriber::STATUS_ACTIVE,
            'subscribed_at' => $subscriber->subscribed_at ?? now(),
            'unsubscribed_at' => null,
        ]);
        $subscriber->save();

        // Always send a welcome / confirmation email (unless already active and they re-submitted within a few minutes)
        if (! $alreadyActive) {
            $dispatcher->send('newsletter.welcome', [
                $subscriber->email,
                null,
                $subscriber->name ?: 'Subscriber',
            ], [
                'name' => $subscriber->name ?: 'Subscriber',
                'unsubscribe_url' => $subscriber->unsubscribeUrl(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $alreadyActive
                    ? "You're already subscribed — thanks for staying with us."
                    : "You're in. Check your inbox for a quick confirmation.",
            ]);
        }

        return back()->with('newsletter_success', $alreadyActive
            ? "You're already subscribed — thanks for staying with us."
            : "You're in. Check your inbox for a quick confirmation.");
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        return view('pages.newsletter.unsubscribe', [
            'subscriber' => $subscriber,
            'invalid' => $subscriber === null,
            'alreadyUnsubscribed' => $subscriber && ! $subscriber->isActive(),
            'pageTitle' => 'Unsubscribe',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Unsubscribe'],
            ],
        ]);
    }

    public function unsubscribeConfirm(string $token, EmailDispatcher $dispatcher)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        if ($subscriber->isActive()) {
            $subscriber->update([
                'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
                'unsubscribed_at' => now(),
            ]);

            $dispatcher->send('newsletter.unsubscribed', [
                $subscriber->email,
                null,
                $subscriber->name ?: 'Subscriber',
            ], [
                'name' => $subscriber->name ?: 'Subscriber',
                'resubscribe_url' => url('/news'),
            ]);
        }

        return redirect()->route('newsletter.unsubscribe', $token)
            ->with('newsletter_success', "You've been unsubscribed. Sorry to see you go.");
    }
}
