@push('scripts')
    <script type="text/javascript" src="https://js.stripe.com/v3"></script>
@endpush

<div class="container mx-auto px-4 sm:px-8 text-xl py-6 md:py-10">
    <h1 class="pt-4 pb-3 text-3xl text-white text-center tracking-normal font-extrabold lg:text-5xl">
        {{ $title ?? 'Choose the most suitable plan' }}
    </h1>
    <div class="my-4 text-center">
        <div class="inline-flex">
            <a href="{{ route($routeName, ['channel' => $channel, 'yearly' => false]) }}">
                <button id="monthly-button"
                    class="rounded-l-lg border-gray-700 border-2 @if ($isYearly === false) bg-gray-700 text-gray-100 @else text-gray-700 @endif focus:outline-none text-sm font-semibold py-1 px-4">
                    Monthly
                </button>
            </a>
            <a href="{{ route($routeName, ['channel' => $channel, 'yearly' => true]) }}">
                <button id="yearly-button"
                    class="rounded-r-lg border-gray-700 border-2 @if ($isYearly === true) bg-gray-700 text-gray-100 @else text-gray-700 @endif focus:outline-none text-sm font-semibold py-1 px-4">
                    Yearly
                </button>
            </a>
        </div>
        <div class="text-sm text-gray-500 leading-tight text-center mt-2">
            @if (!$isYearly)
                Subscribe yearly and get two monthes offered.
            @else
                🎉 Two monthes offered.
            @endif
        </div>
    </div>

    @if (!$plans->count())
        <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
            <p class="font-bold">No plans.</p>
        </div>
    @else
        <div class="md:flex content-center flex-wrap -mx-2 p-3 bg-grey shadow-lg">
            @foreach ($plans as $plan)
                <div class="md:flex md:w-1/2 lg:w-1/3 px-2 sm:px-6 py-2">
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
                                    <span class="billing-price">
                                        @if ($isYearly)
                                            {{ $plan->price * 10 }}
                                        @else
                                            {{ $plan->price }}
                                        @endif
                                    </span>
                                </span>
                                <span class="text-gray-600 billing-period">
                                    @if ($isYearly)
                                        /yr
                                    @else
                                        /mo
                                    @endif
                                </span>
                            </div>
                            <!-- core features -->
                            <div>
                                <ul class="">
                                    <li class="flex items-baseline mb-4">
                                        <span class="ml-2 mr-6 mt-1">
                                            <svg class="h-5 w-5 block" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <g fill="none" fill-rule="evenodd">
                                                    <circle cx="10" cy="10" r="10" fill="#9CE2B6">
                                                    </circle>
                                                    <polyline stroke="#126D34" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        points="6 10 8.667 12.667 14 7.333"></polyline>
                                                </g>
                                            </svg>
                                        </span>
                                        <span>Adding up to <strong>{{ $plan->nb_episodes_per_month }}</strong>
                                            episodes per month</span>
                                    </li>
                                    <li class="flex items-baseline mb-4">
                                        <span class="ml-2 mr-6 mt-1">
                                            <svg class="h-5 w-5 block" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <g fill="none" fill-rule="evenodd">
                                                    <circle cx="10" cy="10" r="10" fill="#9CE2B6">
                                                    </circle>
                                                    <polyline stroke="#126D34" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        points="6 10 8.667 12.667 14 7.333"></polyline>
                                                </g>
                                            </svg>
                                        </span>
                                        <span>Your podcast begin with your last {{ $plan->nb_episodes_per_month }}
                                            episodes</span>
                                    </li>
                                    <li class="flex items-baseline mb-4">
                                        <span class="ml-2 mr-6 mt-1">
                                            <svg class="h-5 w-5 block" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <g fill="none" fill-rule="evenodd">
                                                    <circle cx="10" cy="10" r="10" fill="#9CE2B6">
                                                    </circle>
                                                    <polyline stroke="#126D34" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        points="6 10 8.667 12.667 14 7.333"></polyline>
                                                </g>
                                            </svg>
                                        </span>
                                        <span>Unlimited bandwidth</span>
                                    </li>
                                    <li class="flex items-baseline mb-4">
                                        <span class="ml-2 mr-6 mt-1">
                                            <svg class="h-5 w-5 block" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <g fill="none" fill-rule="evenodd">
                                                    <circle cx="10" cy="10" r="10" fill="#9CE2B6">
                                                    </circle>
                                                    <polyline stroke="#126D34" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        points="6 10 8.667 12.667 14 7.333"></polyline>
                                                </g>
                                            </svg>
                                        </span>
                                        <span>Friendly and reactive support</span>
                                    </li>
                                    <li class="flex items-baseline mb-4">
                                        <span class="ml-2 mr-6 mt-1">
                                            <svg class="h-5 w-5 block" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <g fill="none" fill-rule="evenodd">
                                                    <circle cx="10" cy="10" r="10"
                                                        fill="#9CE2B6"></circle>
                                                    <polyline stroke="#126D34" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        points="6 10 8.667 12.667 14 7.333"></polyline>
                                                </g>
                                            </svg>
                                        </span>
                                        <span>Add exclusive content</span>
                                    </li>
                                </ul>


                                <div class="text-center mt-12 align-bottom">
                                    <button id="{{ $plan->name }}" role="link"
                                        class="w-full text-lg sm:text-xl block rounded-lg text-white focus:outline-none bg-gray-900 focus:bg-gray-700 hover:bg-gray-700 font-semibold px-6 py-3 sm:py-4">
                                        {{ $buttonLabel }}
                                    </button>

                                    <script>
                                        var stripe = Stripe('{{ config('services.stripe.key') }}');

                                        var checkoutButton = document.getElementById('{{ $plan->name }}');
                                        checkoutButton.addEventListener('click', function() {
                                            stripe.redirectToCheckout({
                                                    sessionId: '{{ $plan->stripeSession()->id }}'
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
