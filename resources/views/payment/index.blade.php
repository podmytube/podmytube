@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

    <div class="container text-center">
      
      <form action="/subscribe" method="POST">

        {{ csrf_field() }}

        <script
        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
        data-key="{{ env('STRIPE_KEY') }}"
        data-amount="900"
        data-name="Podmytube"
        data-description="Weekly Youtuber 9€"
        data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
        data-locale="auto"
        date-currency="EUR"
        data-zip-code="true">
        </script>
      </form>

    </div>

@endsection



