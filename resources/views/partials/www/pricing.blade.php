<section id="pricing" class="pricing-section">
    <div class="container py-3 text-center">
        <h2>Pricing</h2>
        <p class="py-1">
            <strong> If you need a customized offer please </strong>
            <i class="far fa-envelope"></i>
            <a href="mailto:contact@podmytube.com">contact me</a>.
        </p>

        <div class="card-deck mb-3 text-center plan-cards">
            @foreach ($plans as $plan)
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-main-colorbg text-white">
                    <h3 class="my-0 font-weight-normal">
                        {{ $plan['name'] }}
                    </h3>
                </div>
                <div class="card-body">
                    <h3 class="card-title pricing-card-title">
                        {{ $plan['price'] }} <i class="fas fa-euro-sign"></i> <small class="text-muted">/ month</small>
                    </h3>
                    <ul class="list-unstyled my-1">
                        
                        @if ($plan['price'] == 0)
                        <li>Only <strong>{{ $plan['nb_episodes_per_month'] }} episodes</strong>/month</li>
                        @else
                        <li>Up to <strong>{{ $plan['nb_episodes_per_month'] }} episodes</strong>/month</li>
                        @endif

                        @if ($plan['price'] == 0)
                        <li>Episodes are stored only 4 monthes.</li>
                        @else
                        <li>No time limitation.</li>
                        @endif

                        
                    </ul>
                </div>
            </div>
            @endforeach
        </div>

        <a class="btn btn-lg btn-success mt-5 text-uppercase" href="{{ route('register') }}">Get started free</a>
        <p class="text-muted">no credit card, cancel anytime</p>
    </div>
</section>