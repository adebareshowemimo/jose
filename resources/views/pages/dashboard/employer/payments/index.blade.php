@extends('layouts.dashboard')

@section('title', 'Payments & Receipts')
@section('page-title', 'Payments & Receipts')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
    @include('pages.dashboard._shared.payments.index')
@endsection
