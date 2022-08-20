@extends('layouts.app')

@section('pageTitle', 'curves')

@section('content')

    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">📈 Analytics ⚡</h2>
        <ul class="bg-gray-100">
            <li>
                <a
                    href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_THIS_MONTH }}">
                    this month</a>
            </li>
            <li>
                <a
                    href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_THIS_WEEK }}">
                    this week</a>
            </li>
            <li>
                <a
                    href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_LAST_MONTH }}">
                    last month</a>
            </li>
            <li>
                <a
                    href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_LAST_WEEK }}">
                    this week</a>
            </li>
        </ul>

        <canvas id="analytics"></canvas>
    </div>

    @push('scripts')
        <script src="{{ asset('js/chart.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function(event) {
                const ctx = document.getElementById('analytics').getContext('2d');
                const myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! $abscissa !!},
                        datasets: [{
                            label: '# of downloads',
                            data: {!! $ordinate !!},
                            cubicInterpolationMode: 'monotone',
                            backgroundColor: [
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderColor: [
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
