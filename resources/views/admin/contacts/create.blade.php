@extends('admin.layouts.app')

@section('title', 'Add Contact')
@section('page-title', 'Add Contact')

@section('content')
    <div class="max-w-4xl rounded-xl border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.contacts.store') }}" class="space-y-6">
            @csrf
            @include('admin.contacts.partials.form', ['contact' => $contact])
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.contacts.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-[#1AAD94] text-white text-sm font-semibold rounded-lg hover:bg-[#158f7a]">Create Contact</button>
            </div>
        </form>
    </div>
@endsection
