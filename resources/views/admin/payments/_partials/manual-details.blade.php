@php $meta = $payment->gateway_response ?? []; @endphp
<dl class="grid sm:grid-cols-2 gap-2 text-xs mt-3 pt-3 border-t border-gray-100">
    @if (! empty($meta['paid_at']))<div><dt class="text-gray-500">Date paid</dt><dd class="text-gray-900">{{ $meta['paid_at'] }}</dd></div>@endif
    @if (! empty($meta['sender_bank']))<div><dt class="text-gray-500">Sender bank</dt><dd class="text-gray-900">{{ $meta['sender_bank'] }}</dd></div>@endif
    @if (! empty($meta['sender_account']))<div><dt class="text-gray-500">Sender account</dt><dd class="text-gray-900">{{ $meta['sender_account'] }}</dd></div>@endif
    @if (! empty($meta['note']))<div class="sm:col-span-2"><dt class="text-gray-500">Customer note</dt><dd class="text-gray-900 whitespace-pre-line">{{ $meta['note'] }}</dd></div>@endif
    @if (! empty($meta['proof_path']))
        <div class="sm:col-span-2"><dt class="text-gray-500">Proof of payment</dt>
            <dd><a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($meta['proof_path']) }}" target="_blank" class="text-[#1AAD94] font-semibold">Download &rarr;</a></dd>
        </div>
    @endif
    @if (! empty($meta['submitted_at']))<div><dt class="text-gray-500">Submitted at</dt><dd class="text-gray-900">{{ \Carbon\Carbon::parse($meta['submitted_at'])->format('M d, Y g:i A') }}</dd></div>@endif
    @if (! empty($meta['verified_at']))<div><dt class="text-gray-500">Verified at</dt><dd class="text-emerald-700">{{ \Carbon\Carbon::parse($meta['verified_at'])->format('M d, Y g:i A') }}</dd></div>@endif
    @if (! empty($meta['rejected_at']))<div><dt class="text-gray-500">Rejected at</dt><dd class="text-red-700">{{ \Carbon\Carbon::parse($meta['rejected_at'])->format('M d, Y g:i A') }}</dd></div>@endif
    @if (! empty($meta['rejection_reason']))<div class="sm:col-span-2"><dt class="text-gray-500">Rejection reason</dt><dd class="text-red-600">{{ $meta['rejection_reason'] }}</dd></div>@endif
</dl>
