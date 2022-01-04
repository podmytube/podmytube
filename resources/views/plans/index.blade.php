@extends('layouts.app')

@section('pageTitle', 'Consider upgrading your plan 🤗')

@section('stripeJs')
    <!-- Load Stripe.js on your website. -->
    <script src="https://js.stripe.com/v3"></script>
@endsection


@section('content')
    @include('partials.plans');
@endsection
