@extends('layouts.dashboard')

@section('title', 'My Payments')
@section('page-title', 'My Payments')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
    @include('pages.dashboard._shared.payments.index')
@endsection
