<!-- Create a button that your customers click to complete their purchase. -->
<button id="{{ $buttonId }}" class="btn btn-lg btn-block btn-success" role="link">{{ __('plans.pricing_button_subscribe') }}</button>

<script>
  var stripe = Stripe('{{ env("STRIPE_KEY") }}');

  var checkoutButton = document.getElementById('{{ $buttonId }}');
  checkoutButton.addEventListener('click', function() {
    stripe.redirectToCheckout({
        sessionId: '{{ $checkout_session_id }}'
      })
      .then(function(result) {
        if (result.error) {
          // If `redirectToCheckout` fails due to a browser or network
          // error, display the localized error message to your customer.
          var displayError = document.getElementById('error-message');
          displayError.textContent = result.error.message;
        }
      });
  });
</script>