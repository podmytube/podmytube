@extends('layouts.app')

@section('pageTitle', 'Consider upgrading your plan 🤗')

@section('stripeJs')
<!-- Load Stripe.js on your website. -->
<script src="https://js.stripe.com/v3"></script>
@endsection

@section ('content')

<div class="max-w-screen-lg mx-auto px-8 text-xl py-16">

    <h1 class="text-center text-3xl pb-6 md:text-5xl text-white font-semibold">Upgrade your plan</h1>
    
    <div class="flex rounded-lg shadow-lg p-8 bg-white text-gray-100 ">

                    @foreach ($plans as $plan)
                    <div class="flex-auto bg-gray-900">
                        <h4 class="my-0 font-weight-normal">{!! __('plans.'.$plan->name) !!}</h4>
                        <h1 class="card-title pricing-card-title">{!! __('plans.price_monthly', ['price' => $plan->price]) !!}</h1>
                        <ul class="">
                          {!! __('plans.episodes_per_month', ['nb' => $plan->nb_episodes_per_month]) !!}
                          {!! __('plans.standard_paying_features') !!}
                        </ul>

                        @include("partials.stripeButton", [
                        "buttonId" => $plan->name,
                        "checkout_session_id" => $plan->stripeSession->id,
                        ])

                      </div>

                    @endforeach
    </div>
</div>

@endsection