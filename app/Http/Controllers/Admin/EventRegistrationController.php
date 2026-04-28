<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventRegistrationController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $query = $event->registrations()->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('buyer_email', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(25)->withQueryString();
        $stats = [
            'total' => $event->registrations()->count(),
            'paid' => $event->registrations()->where('status', 'paid')->count(),
            'tickets' => (int) $event->registrations()->where('status', 'paid')->sum('ticket_count'),
            'capacity' => $event->capacity,
        ];

        return view('admin.events.registrations', compact('event', 'registrations', 'stats'));
    }

    public function export(Event $event): StreamedResponse
    {
        return response()->streamDownload(function () use ($event) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Email', 'Phone', 'Tickets', 'Status', 'Registered', 'Order #', 'Answers']);

            $event->registrations()->with('order')->orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->buyer_name,
                        $r->buyer_email,
                        $r->buyer_phone,
                        $r->ticket_count,
                        $r->status,
                        $r->registered_at?->toDateTimeString(),
                        $r->order?->order_number,
                        json_encode($r->answers),
                    ]);
                }
            });

            fclose($out);
        }, "event-{$event->id}-attendees-" . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
