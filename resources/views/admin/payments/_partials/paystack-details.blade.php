@php
    $resp = $payment->gateway_response ?? [];
    $verified = $resp['verified'] ?? null;
    $auth = $verified['authorization'] ?? null;
@endphp
<dl class="grid sm:grid-cols-2 gap-2 text-xs mt-3 pt-3 border-t border-gray-100">
    @if (! empty($verified['reference']))<div><dt class="text-gray-500">Paystack reference</dt><dd class="font-mono text-gray-900">{{ $verified['reference'] }}</dd></div>@endif
    @if (! empty($verified['channel']))<div><dt class="text-gray-500">Channel</dt><dd class="text-gray-900">{{ ucfirst($verified['channel']) }}</dd></div>@endif
    @if (! empty($auth['bank']))<div><dt class="text-gray-500">Bank</dt><dd class="text-gray-900">{{ $auth['bank'] }}</dd></div>@endif
    @if (! empty($auth['card_type']))<div><dt class="text-gray-500">Card type</dt><dd class="text-gray-900">{{ ucfirst($auth['card_type']) }}</dd></div>@endif
    @if (! empty($auth['last4']))<div><dt class="text-gray-500">Card last 4</dt><dd class="font-mono text-gray-900">**** **** **** {{ $auth['last4'] }}</dd></div>@endif
    @if (! empty($verified['customer']['email']))<div><dt class="text-gray-500">Paystack customer email</dt><dd class="text-gray-900">{{ $verified['customer']['email'] }}</dd></div>@endif
    @if (! empty($verified['fees']))<div><dt class="text-gray-500">Paystack fees</dt><dd class="text-gray-900">{{ ($payment->currency ?? 'USD') }} {{ number_format(((int) $verified['fees']) / 100, 2) }}</dd></div>@endif
    @if (! empty($verified['paid_at']))<div><dt class="text-gray-500">Paid at (Paystack)</dt><dd class="text-gray-900">{{ \Carbon\Carbon::parse($verified['paid_at'])->format('M d, Y g:i A') }}</dd></div>@endif
    @if (! empty($verified['ip_address']))<div><dt class="text-gray-500">IP address</dt><dd class="font-mono text-gray-900">{{ $verified['ip_address'] }}</dd></div>@endif
</dl>

@if (! empty($resp))
    <details class="mt-3 pt-3 border-t border-gray-100">
        <summary class="text-xs font-semibold text-gray-500 cursor-pointer hover:text-gray-700">Raw gateway response (JSON)</summary>
        <pre class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded text-[10px] text-gray-700 overflow-auto max-h-80">{{ json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </details>
@endif
