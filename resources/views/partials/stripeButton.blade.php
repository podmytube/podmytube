<!-- Create a button that your customers click to complete their purchase. -->
<button id="{{ $buttonId }}" role="link"
    class="w-full text-lg sm:text-xl block rounded-lg text-white focus:outline-none bg-gray-900 focus:bg-gray-700 hover:bg-gray-700 font-semibold px-6 py-3 sm:py-4">
    {{ $label  }}
</button>

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