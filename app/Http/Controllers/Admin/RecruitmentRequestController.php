<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuoteRecruitmentRequest;
use App\Models\Candidate;
use App\Models\ChatConversation;
use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestCandidate;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecruitmentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = RecruitmentRequest::with(['company', 'requester', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->input('service_type'));
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('admin.recruitment-requests.index', compact('requests'));
    }

    public function show(RecruitmentRequest $recruitment)
    {
        $recruitment->load([
            'company', 'requester', 'category', 'jobType', 'location',
            'order', 'assignedAdmin',
            'candidates.candidate.user',
        ]);

        $jobNotificationTemplates = EmailTemplate::where('is_active', true)
            ->whereIn('category', ['Recruitment', 'Job Notification'])
            ->orderBy('name')
            ->get(['key', 'name']);

        return view('admin.recruitment-requests.show', [
            'recruitment' => $recruitment,
            'jobNotificationTemplates' => $jobNotificationTemplates,
        ]);
    }

    public function update(Request $request, RecruitmentRequest $recruitment)
    {
        $data = $request->validate([
            'status' => 'sometimes|in:pending,quote_sent,paid,in_progress,candidates_delivered,completed,cancelled',
            'admin_notes' => 'nullable|string|max:5000',
            'assigned_to_admin_user_id' => 'nullable|exists:users,id',
        ]);

        $recruitment->update($data);

        return back()->with('success', 'Request updated.');
    }

    public function quote(QuoteRecruitmentRequest $request, RecruitmentRequest $recruitment, EmailDispatcher $dispatcher)
    {
        $data = $request->validated();

        if (in_array($recruitment->status, ['cancelled', 'completed'], true)) {
            return back()->with('error', 'This request can no longer be quoted.');
        }

        DB::transaction(function () use ($data, $recruitment) {
            $order = Order::create([
                'order_number' => 'REC-' . strtoupper(Str::random(8)),
                'user_id' => $recruitment->requested_by_user_id,
                'subtotal' => $data['quoted_amount'],
                'tax' => 0,
                'total' => $data['quoted_amount'],
                'currency' => $data['currency'],
                'gateway' => 'manual',
                'status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'orderable_type' => RecruitmentRequest::class,
                'orderable_id' => $recruitment->id,
                'price' => $data['quoted_amount'],
                'quantity' => 1,
                'subtotal' => $data['quoted_amount'],
                'status' => 'pending',
                'meta' => [
                    'service_type' => $recruitment->service_type,
                    'cv_count' => $recruitment->cv_count,
                    'job_title' => $recruitment->job_title,
                ],
            ]);

            $recruitment->update([
                'status' => 'quote_sent',
                'quoted_amount' => $data['quoted_amount'],
                'salary_currency' => $data['currency'],
                'quoted_at' => now(),
                'order_id' => $order->id,
                'assigned_to_admin_user_id' => $recruitment->assigned_to_admin_user_id ?? Auth::id(),
            ]);
        });

        if ($recruitment->requester) {
            $dispatcher->send('recruitment.quote_sent', $recruitment->requester, [
                'job_title' => $recruitment->job_title,
                'quoted_amount' => number_format((float) $data['quoted_amount'], 2),
                'currency' => $data['currency'],
                'quote_note' => $data['quote_note'] ?? '',
                'order_url' => route('order.detail', $recruitment->order_id),
            ]);
        }

        return back()->with('success', 'Quote issued and email sent.');
    }

    public function attachCandidate(Request $request, RecruitmentRequest $recruitment, EmailDispatcher $dispatcher)
    {
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'summary' => 'nullable|string|max:2000',
        ]);

        $candidate = Candidate::find($data['candidate_id']);

        $delivery = RecruitmentRequestCandidate::create([
            'recruitment_request_id' => $recruitment->id,
            'candidate_id' => $candidate->id,
            'summary' => $data['summary'] ?? null,
            'employer_decision' => 'pending',
            'delivered_at' => now(),
        ]);

        ChatConversation::firstOrCreate(
            [
                'type' => ChatConversation::TYPE_EMPLOYER_CANDIDATE,
                'recruitment_request_candidate_id' => $delivery->id,
            ],
            [
                'company_id' => $recruitment->company_id,
                'candidate_id' => $candidate->id,
                'started_by_user_id' => Auth::id(),
                'last_message_at' => now(),
            ]
        );

        $this->maybeAdvanceToDelivered($recruitment, $dispatcher);

        return back()->with('success', 'Candidate attached.');
    }

    public function uploadCv(Request $request, RecruitmentRequest $recruitment, EmailDispatcher $dispatcher)
    {
        $data = $request->validate([
            'external_name' => 'required|string|max:255',
            'external_email' => 'nullable|email|max:255',
            'external_phone' => 'nullable|string|max:50',
            'summary' => 'nullable|string|max:2000',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $path = $request->file('cv_file')->store("recruitment-requests/{$recruitment->id}/cv", 'public');

        RecruitmentRequestCandidate::create([
            'recruitment_request_id' => $recruitment->id,
            'external_name' => $data['external_name'],
            'external_email' => $data['external_email'] ?? null,
            'external_phone' => $data['external_phone'] ?? null,
            'external_cv_path' => $path,
            'summary' => $data['summary'] ?? null,
            'employer_decision' => 'pending',
            'delivered_at' => now(),
        ]);

        $this->maybeAdvanceToDelivered($recruitment, $dispatcher);

        return back()->with('success', 'External CV uploaded.');
    }

    public function removeCandidate(RecruitmentRequest $recruitment, RecruitmentRequestCandidate $candidate)
    {
        if ($candidate->recruitment_request_id !== $recruitment->id) {
            abort(404);
        }
        $candidate->delete();
        return back()->with('success', 'Candidate removed.');
    }

    public function notify(Request $request, RecruitmentRequest $recruitment, EmailDispatcher $dispatcher)
    {
        $data = $request->validate([
            'template_key' => 'required|exists:email_templates,key',
            'message' => 'nullable|string|max:5000',
        ]);

        if (! $recruitment->requester) {
            return back()->with('error', 'No requester email on file.');
        }

        $ok = $dispatcher->send($data['template_key'], $recruitment->requester, [
            'job_title' => $recruitment->job_title,
            'company_name' => $recruitment->company?->name,
            'message' => $data['message'] ?? '',
            'request_url' => route('employer.recruitment-requests.show', $recruitment),
            'candidate_count' => $recruitment->candidates->count(),
        ]);

        return back()->with($ok ? 'success' : 'error',
            $ok ? 'Email sent.' : 'Failed to send email — see logs.');
    }

    protected function maybeAdvanceToDelivered(RecruitmentRequest $recruitment, EmailDispatcher $dispatcher): void
    {
        if ($recruitment->status === 'in_progress' || $recruitment->status === 'paid') {
            $recruitment->update(['status' => 'candidates_delivered']);

            if ($recruitment->requester) {
                $dispatcher->send('recruitment.candidates_delivered', $recruitment->requester, [
                    'job_title' => $recruitment->job_title,
                    'candidate_count' => $recruitment->candidates()->count(),
                    'request_url' => route('employer.recruitment-requests.show', $recruitment),
                ]);
            }
        }
    }
}
