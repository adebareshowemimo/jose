<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostPackage;
use App\Models\CandidateBoost;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BoostSubscriberController extends Controller
{
    /**
     * Boosts ending within this many days count as "expiring soon".
     */
    private const EXPIRING_WINDOW_DAYS = 7;

    public function index(Request $request)
    {
        $boosts = $this->filtered($request)
            ->with(['candidate.user', 'package', 'order'])
            ->latest('starts_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.boosts.subscribers.index', [
            'boosts' => $boosts,
            'packages' => BoostPackage::ordered()->get(),
            'stats' => $this->stats(),
            'expiringWindow' => self::EXPIRING_WINDOW_DAYS,
        ]);
    }

    public function show(CandidateBoost $boost)
    {
        $boost->load(['candidate.user', 'package', 'order.payments', 'order.items']);

        return view('admin.boosts.subscribers.show', [
            'boost' => $boost,
            'history' => CandidateBoost::where('candidate_id', $boost->candidate_id)
                ->where('id', '!=', $boost->id)
                ->with('package')
                ->latest('starts_at')
                ->get(),
        ]);
    }

    /**
     * Extend a running boost by a number of days.
     *
     * Pushes both the boost row and the candidate's featured_until, since the
     * latter is what actually governs placement.
     */
    public function extend(Request $request, CandidateBoost $boost)
    {
        $data = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $days = (int) $data['days'];

        // Extend from whichever is later: the current end date, or now. A
        // boost that already lapsed should not be back-dated into the past.
        $base = $boost->ends_at && $boost->ends_at->isFuture() ? $boost->ends_at : now();
        $newEnd = $base->copy()->addDays($days);

        $boost->update([
            'ends_at' => $newEnd,
            'days' => $boost->days + $days,
            'status' => CandidateBoost::STATUS_ACTIVE,
        ]);

        $candidate = $boost->candidate;
        if ($candidate) {
            $featuredBase = $candidate->featured_until && $candidate->featured_until->isFuture()
                ? $candidate->featured_until
                : now();

            // Only push featured_until forward if this boost now outlasts it.
            if ($newEnd->greaterThan($featuredBase)) {
                $candidate->update(['featured_until' => $newEnd]);
            }
        }

        return redirect()
            ->route('admin.boosts.subscribers.show', $boost)
            ->with('success', "Extended by {$days} day(s). Now ends {$newEnd->format('M d, Y')}.");
    }

    /**
     * End a boost immediately.
     *
     * revoke_placement decides whether the candidate loses top placement now
     * or keeps the time already paid for. Default is to let it run out.
     */
    public function cancel(Request $request, CandidateBoost $boost)
    {
        $request->validate([
            'status' => ['required', 'in:expired,refunded'],
            'revoke_placement' => ['nullable', 'boolean'],
        ]);

        $boost->update(['status' => $request->input('status')]);

        $message = 'Boost marked as ' . $request->input('status') . '.';

        if ($request->boolean('revoke_placement') && $boost->candidate) {
            $candidate = $boost->candidate;

            // Fall back to the latest other active boost, so revoking one
            // boost does not strip placement paid for by another.
            $other = CandidateBoost::where('candidate_id', $candidate->id)
                ->where('id', '!=', $boost->id)
                ->where('status', CandidateBoost::STATUS_ACTIVE)
                ->whereNotNull('ends_at')
                ->where('ends_at', '>', now())
                ->orderByDesc('ends_at')
                ->first();

            $candidate->update(['featured_until' => $other?->ends_at]);

            $message .= $other
                ? ' Placement now runs to the candidate\'s other active boost.'
                : ' Featured placement removed immediately.';
        }

        return redirect()
            ->route('admin.boosts.subscribers.show', $boost)
            ->with('success', $message);
    }

    public function export(Request $request): StreamedResponse
    {
        $boosts = $this->filtered($request)
            ->with(['candidate.user', 'package', 'order'])
            ->latest('starts_at')
            ->get();

        $filename = 'boost-subscribers-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($boosts) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'Boost ID', 'Candidate', 'Email', 'Package', 'Days',
                'Starts', 'Ends', 'Days remaining', 'Status', 'Amount', 'Order',
            ]);

            foreach ($boosts as $boost) {
                fputcsv($out, [
                    $boost->id,
                    $boost->candidate?->user?->name ?? '',
                    $boost->candidate?->user?->email ?? '',
                    $boost->package?->label ?? '',
                    $boost->days,
                    $boost->starts_at?->toDateString() ?? '',
                    $boost->ends_at?->toDateString() ?? '',
                    $boost->ends_at && $boost->ends_at->isFuture()
                        ? now()->diffInDays($boost->ends_at)
                        : 0,
                    $boost->status,
                    $boost->price,
                    $boost->order?->order_number ?? '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function filtered(Request $request)
    {
        $query = CandidateBoost::query();

        // "active" and "expiring" are derived from dates as well as status, so
        // a row the hourly expiry sweep has not reached yet is not miscounted.
        switch ($request->input('status')) {
            case 'active':
                $query->where('status', CandidateBoost::STATUS_ACTIVE)
                    ->where('ends_at', '>', now());
                break;
            case 'expiring':
                $query->where('status', CandidateBoost::STATUS_ACTIVE)
                    ->whereBetween('ends_at', [now(), now()->addDays(self::EXPIRING_WINDOW_DAYS)]);
                break;
            case 'expired':
                $query->where(function ($q) {
                    $q->where('status', CandidateBoost::STATUS_EXPIRED)
                        ->orWhere(function ($q2) {
                            $q2->where('status', CandidateBoost::STATUS_ACTIVE)
                                ->where('ends_at', '<=', now());
                        });
                });
                break;
            case 'refunded':
                $query->where('status', CandidateBoost::STATUS_REFUNDED);
                break;
        }

        if ($request->filled('package')) {
            $query->where('boost_package_id', $request->input('package'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->input('q') . '%';
            $query->whereHas('candidate.user', function ($u) use ($term) {
                $u->where('name', 'like', $term)->orWhere('email', 'like', $term);
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('starts_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('starts_at', '<=', $request->input('to'));
        }

        return $query;
    }

    private function stats(): array
    {
        $activeQuery = CandidateBoost::where('status', CandidateBoost::STATUS_ACTIVE)
            ->where('ends_at', '>', now());

        return [
            'active' => (clone $activeQuery)->count(),
            'expiring' => CandidateBoost::where('status', CandidateBoost::STATUS_ACTIVE)
                ->whereBetween('ends_at', [now(), now()->addDays(self::EXPIRING_WINDOW_DAYS)])
                ->count(),
            'total' => CandidateBoost::count(),
            // Revenue counts every boost ever sold, refunds excluded.
            'revenue' => (float) CandidateBoost::where('status', '!=', CandidateBoost::STATUS_REFUNDED)
                ->sum('price'),
        ];
    }
}
