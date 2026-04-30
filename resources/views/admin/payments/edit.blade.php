@extends('admin.layouts.app')

@section('title', 'Edit Payment #' . $payment->id)
@section('page-title', 'Edit Payment')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.payments.show', $payment) }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payment
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="max-w-2xl bg-white rounded-xl border border-gray-200 p-6">
        <div class="mb-5 pb-5 border-b border-gray-100">
            <p class="text-xs uppercase tracking-widest text-gray-400">Payment</p>
            <p class="text-lg font-bold text-gray-900">#{{ $payment->id }} — {{ money($payment->amount, $payment->currency ?? 'USD') }}</p>
            <p class="text-sm text-gray-500">Order <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-[#1AAD94] hover:underline">#{{ $payment->order?->order_number }}</a> · Customer {{ $payment->order?->user?->name ?? '—' }}</p>
        </div>

        <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                    @foreach (['pending','processing','completed','failed','refunded'] as $s)
                        <option value="{{ $s }}" {{ old('status', $payment->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Setting status to <strong>completed</strong> will also stamp the order as paid (if not already).</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Transaction ID</label>
                <input type="text" name="transaction_id" value="{{ old('transaction_id', $payment->transaction_id) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none font-mono" />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Admin note (appended)</label>
                <textarea name="admin_note" rows="3" placeholder="Optional internal note recorded with this change."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ old('admin_note') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Stored in the payment's gateway response history. Not shown to customers.</p>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                <a href="{{ route('admin.payments.show', $payment) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-5 py-2 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">Save changes</button>
            </div>
        </form>
    </div>
@endsection
