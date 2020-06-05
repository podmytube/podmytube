<!-- Create a button that your customers click to complete their purchase. -->
<button id="{{ $buttonId }}" class="btn btn-lg btn-block btn-success" role="link">{{ __('plans.pricing_button_subscribe') }}</button>

<script>
  var stripe = Stripe('{{ env("STRIPE_KEY") }}', {
    betas: ['checkout_beta_4']
  });

  var checkoutButton = document.getElementById('{{ $buttonId }}');
  checkoutButton.addEventListener('click', function () {
    // When the customer clicks on the button, redirect
    // them to Checkout.
    stripe.redirectToCheckout({
      items: [{plan: '{{ $planToSubscribe }}', quantity: 1}],

      // Note that it is not guaranteed your customers will be redirected to this
      // URL *100%* of the time, it's possible that they could e.g. close the
      // tab between form submission and the redirect.
      successUrl: '{{ $successUrl }}',
      cancelUrl: '{{ $cancelUrl }}',
    })
    .then(function (result) {
      if (result.error) {
        // If `redirectToCheckout` fails due to a browser or network
        // error, display the localized error message to your customer.
        var displayError = document.getElementById('error-message');
        displayError.textContent = result.error.message;
      }
    });
  });
</script>