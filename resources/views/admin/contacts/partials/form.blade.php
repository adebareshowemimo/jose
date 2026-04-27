@php
    $statusOptions = ['new', 'in_progress', 'customer_replied', 'resolved', 'closed', 'spam'];
    $priorityOptions = ['low', 'normal', 'high', 'urgent'];
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Name</label>
        <input type="text" name="name" value="{{ old('name', $contact?->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Email</label>
        <input type="email" name="email" value="{{ old('email', $contact?->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $contact?->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Category</label>
        <input type="text" name="category" value="{{ old('category', $contact?->category ?? 'General Inquiry') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div class="md:col-span-2">
        <label class="text-xs font-medium text-gray-500 mb-1 block">Subject</label>
        <input type="text" name="subject" value="{{ old('subject', $contact?->subject) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ old('status', $contact?->status ?? 'new') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Priority</label>
        <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            @foreach($priorityOptions as $priority)
                <option value="{{ $priority }}" {{ old('priority', $contact?->priority ?? 'normal') === $priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="text-xs font-medium text-gray-500 mb-1 block">Initial Message</label>
        <textarea name="message" rows="7" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('message', $contact?->message) }}</textarea>
    </div>
</div>
