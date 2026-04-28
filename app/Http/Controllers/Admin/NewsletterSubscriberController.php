<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $subscribers = $query->latest('id')->paginate(25)->withQueryString();

        $stats = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::active()->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', NewsletterSubscriber::STATUS_UNSUBSCRIBED)->count(),
            'last_30_days' => NewsletterSubscriber::active()->where('subscribed_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.newsletter.index', compact('subscribers', 'stats'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', "Removed {$subscriber->email} from the subscriber list.");
    }

    public function unsubscribe(NewsletterSubscriber $subscriber)
    {
        if ($subscriber->isActive()) {
            $subscriber->update([
                'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
                'unsubscribed_at' => now(),
            ]);
        }
        return back()->with('success', "{$subscriber->email} has been unsubscribed.");
    }

    public function reactivate(NewsletterSubscriber $subscriber)
    {
        if (! $subscriber->isActive()) {
            $subscriber->update([
                'status' => NewsletterSubscriber::STATUS_ACTIVE,
                'unsubscribed_at' => null,
                'subscribed_at' => $subscriber->subscribed_at ?? now(),
            ]);
        }
        return back()->with('success', "{$subscriber->email} has been reactivated.");
    }

    public function export(Request $request): StreamedResponse
    {
        $status = $request->input('status');

        return response()->streamDownload(function () use ($status) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Name', 'Status', 'Source', 'Subscribed At', 'Unsubscribed At']);

            NewsletterSubscriber::query()
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderBy('id')
                ->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $row) {
                        fputcsv($out, [
                            $row->email,
                            $row->name,
                            $row->status,
                            $row->source,
                            $row->subscribed_at?->toDateTimeString(),
                            $row->unsubscribed_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($out);
        }, 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
