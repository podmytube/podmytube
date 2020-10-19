<div class="px-8 py-6 bg-gray-200">
    <h3 class="uppercase tracking-wide text-lg sm:text-xl text-center font-bold my-0">
        {{ $plan_title }}
    </h3>
</div>
<div class="px-8 pb-8 text-base md:text-lg">
    <!-- price -->
    <div class="text-center py-4">
        <span
            class="inline-flex items-center font-display text-4xl md:text-5xl font-bold text-black mr-2 sm:mr-3">
            <span class="text-xl md:text-2xl">&euro;</span><span class="billing-price">{{ $plan_monthly_price }}</span>
        </span>
        <span class="text-gray-600 billing-period">/mo</span>
    </div>
    <!-- core features -->
    <div>
        <ul class="px-2 sm:px-6">
            @foreach ($plan_features as $plan_feature)
            <li class="flex items-start mb-4">
                <span class="mr-4 mt-1">
                    @include('svg.check_mark',['cssClass'=>'h-5 w-5 block'])
                </span>
                <span><strong>{{ $plan_feature }}</strong></span>
            </li>
            @endforeach
            @include ('partials.pricing_premium_items')
        </ul>

        <!-- call to action -->
        <div class="text-center mt-12">
            <a class="w-full text-lg sm:text-xl block rounded-lg text-white focus:outline-none bg-gray-900 focus:bg-gray-700 hover:bg-gray-700 font-semibold px-6 py-3 sm:py-4"
                href="{{ route('register') }}">
                Register now
            </a>
        </div>
    </div>
</div>