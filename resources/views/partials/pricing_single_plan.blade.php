<div class="md:flex-1 rounded-t-lg shadow-lg bg-white">
    <div class="bg-gray-200 rounded-t-lg px-8 py-6">
        <h3 class="uppercase tracking-wide text-lg sm:text-xl text-center font-bold my-0">
            {{ $plan['title'] }}
        </h3>
    </div>
    <div class="bg-white rounded-b-lg pr-8 pl-6 pb-8 text-base md:text-lg">
        <!-- price -->
        <div class="text-center py-4">
            <span
                class="inline-flex items-center font-display text-4xl md:text-5xl font-bold text-black mr-2 sm:mr-3">
                <span class="text-xl text-gray-600 md:text-2xl mr-2">&euro;</span>
                <span class="billing-price">{{ $plan['monthly_price'] }}</span>
            </span>
            <span class="text-gray-600 billing-period">/mo</span>
        </div>
        <!-- core features -->
        <div>
            <ul class="">
                @foreach ($plan['features'] as $plan_feature)
                <li class="flex items-baseline mb-4">
                    <span class="ml-2 mr-6 mt-1">
                    @if ($plan_feature['value'])
                        @include('svg.true',['cssClass'=>'h-5 w-5 block'])
                    @else
                        @include('svg.false',['cssClass'=>'h-5 w-5 block'])
                    @endif
                    </span>
                    <span>{!! $plan_feature['desc'] !!}</span>
                </li>
                @endforeach
            </ul>
            <!-- call to action -->
            <div class="text-center mt-12 align-bottom">
                <a class="w-full text-lg sm:text-xl block rounded-lg text-white focus:outline-none bg-gray-900 focus:bg-gray-700 hover:bg-gray-700 font-semibold px-6 py-3 sm:py-4"
                    href="{{ route('register') }}">
                    @guest
                    Register now
                    @else
                    Upgrade
                    @endguest
                </a>
            </div>
        </div>
    </div>
</div>