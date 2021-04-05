@extends('layouts.app')

@section('pageTitle', 'Consider upgrading your plan 🤗')

@section('stripeJs')
<!-- Load Stripe.js on your website. -->
<script src="https://js.stripe.com/v3"></script>
@endsection

@section ('content')

<div class="container mx-auto px-8 text-xl py-6 md:py-10">
    
    <div class="text-center">
        <h1 class="pt-4 pb-3 text-3xl text-white tracking-normal font-extrabold lg:text-5xl">
        Pricing
        </h1>
        <div class="my-4 text-center">
            <div class="inline-flex">
                <a href="{{ route('plans.index', ['channel' => $channel , 'yearly'=>0] ) }}">
                    <button id="monthly-button" class="rounded-l-lg border-gray-700 border-2 bg-gray-700 text-white focus:outline-none text-sm font-semibold py-1 px-4">
                        Monthly
                    </button>
                </a>
                <a href="{{ route('plans.index', ['channel' => $channel , 'yearly'=>1] ) }}">
                    <button id="yearly-button" class="rounded-r-lg border-gray-700 border-2 text-gray-700 focus:outline-none text-sm font-semibold py-1 px-4">
                        Yearly
                    </button>
                </a>
            </div>
            <div class="text-sm text-gray-600 leading-tight text-center mt-2">
                Subscribe yearly and get two monthes free.
            </div>
        </div>
    </div>

    <div class="md:flex content-center flex-wrap -mx-2 p-3 bg-grey rounded shadow-lg">
        @foreach ($plans as $plan)
        <div class="md:flex md:w-1/2 lg:w-1/3 px-2 py-2">
            <div class="md:flex-1 rounded-t-lg shadow-lg bg-white">
                <div class="bg-gray-200 rounded-t-lg px-8 py-6">
                    <h3 class="uppercase tracking-wide text-lg sm:text-xl text-center font-bold my-0">
                        {{ $plan->name }}
                    </h3>
                </div>
                <div class="bg-white rounded-b-lg pr-8 pl-6 pb-8 text-base md:text-lg">
                    <!-- price -->
                    <div class="text-center py-4">
                        <span
                            class="inline-flex items-center font-display text-4xl md:text-5xl font-bold text-black mr-2 sm:mr-3">
                            <span class="text-xl text-gray-600 md:text-2xl mr-2">&euro;</span>
                            <span class="billing-price">{{ $plan->price }}</span>
                        </span>
                        <span class="text-gray-600 billing-period">/mo</span>
                    </div>
                    <!-- core features -->
                    <div>
                        <ul class="">
                            <li class="flex items-baseline mb-4">
                                <span class="ml-2 mr-6 mt-1"> @include('svg.true',['cssClass'=>'h-5 w-5 block']) </span>
                                <span>Adding up to <strong>{{ $plan->nb_episodes_per_month }}</strong> episodes per month</span>
                            </li>
                            <li class="flex items-baseline mb-4">
                                <span class="ml-2 mr-6 mt-1"> @include('svg.true',['cssClass'=>'h-5 w-5 block']) </span>
                                <span>Your podcast begin with your last {{ $plan->nb_episodes_per_month }} episodes</span>
                            </li>
                            <li class="flex items-baseline mb-4">
                                <span class="ml-2 mr-6 mt-1"> @include('svg.true',['cssClass'=>'h-5 w-5 block']) </span>
                                <span>Add exclusive content (coming soon)</span>
                            </li>
                        </ul>

                        
                    <div class="text-center mt-12 align-bottom">
                        @include("partials.stripeButton", [
                            "buttonId" => $plan->name,
                            "checkout_session_id" => $plan->stripeSession->id,
                            "label" => "Upgrade",
                            ])
                    </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection