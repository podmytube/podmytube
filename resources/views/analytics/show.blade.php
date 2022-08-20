@extends('layouts.app')

@section('pageTitle', 'curves')

@section('content')

    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">📈 Analytics ⚡</h2>

        <p class="bg-gray-100">
            <a href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_THIS_MONTH }}">
                this month
            </a>
            <a href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_THIS_WEEK }}">
                this week
            </a>
            <a href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_LAST_MONTH }}">
                last month
            </a>
            <a href="{{ route('analytics', $channel) }}?p={{ App\Http\Controllers\AnalyticsController::PERIOD_LAST_WEEK }}">
                this week
            </a>
        </p>

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
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
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
