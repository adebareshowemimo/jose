@extends('admin.layouts.app')

@section('title', 'Edit Contact')
@section('page-title', 'Edit Contact')

@section('content')
    <div class="max-w-4xl rounded-xl border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.contacts.update', $contact) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.contacts.partials.form', ['contact' => $contact])
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.contacts.show', $contact) }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:bg-[#073057]/90">Save Changes</button>
            </div>
        </form>
    </div>
@endsection
