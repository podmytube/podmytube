@extends('layouts.app')

@section('pageTitle', "What's the price of your time ?")

@section('content')

    <div class="container mx-auto px-1 md:px-8 text-xl py-6 md:py-10">
        @include('partials.pricing_header')
        <div id="withYearlyPrices" class="md:flex content-center flex-wrap -mx-2 p-3 bg-grey rounded shadow-lg">
            @foreach ($plans as $plan)
                <div class="md:flex md:w-1/2 lg:w-1/3 px-2 py-2">
                    @include ('partials.pricing_single_plan', ['plan' => $plan])
                </div>
            @endforeach
        </div>
    </div>
@endsection
