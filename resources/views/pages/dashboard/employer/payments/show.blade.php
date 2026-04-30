@extends('layouts.dashboard')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
    @include('pages.dashboard._shared.payments.show')
@endsection
