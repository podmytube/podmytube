<div>
    <div>
        <div x-data="{
            open: false,
            selectedPeriod: @entangle('selectedPeriod'),
            selectedPeriodLabel: @entangle('selectedPeriodLabel'),
            abscissa: @entangle('abscissa'),
            ordinate: @entangle('ordinate'),
            init() {
                const ctx = document.getElementById('analytics');
        
                const config = {
                    type: 'line',
                    data: {
                        labels: this.abscissa,
                        datasets: [{
                            label: `${this.selectedPeriodLabel} downloads`,
                            data: this.ordinate,
                            cubicInterpolationMode: 'monotone',
                            backgroundColor: 'rgba(255, 172, 51, 1)',
                            borderColor: 'rgba(255, 172, 51, 1)',
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
                };
        
                const myChart = new Chart(ctx, config);
        
                Livewire.on('updateChartsData', () => {
                    myChart.data.labels = this.abscissa;
                    myChart.data.datasets[0].label = `${this.selectedPeriodLabel} downloads`;
                    myChart.data.datasets[0].data = this.ordinate;
        
                    myChart.update();
                });
            }
        }" class="mt-1 relative">
            <button type="button" x-on:click="open = ! open"
                class="bg-white relative w-full border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label">
                <span class="block truncate"> {{ $selectedPeriodLabel }} </span>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <!-- Heroicon name: solid/selector -->
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </button>



            <ul x-show="open" x-on:click.outside="open = false"
                class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-option-3">
                <!--
        Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation.

        Highlighted: "text-white bg-indigo-600", Not Highlighted: "text-gray-900"
      -->
                @foreach ($this->periods as $index => $label)
                    <li wire:click="selectingPeriod({{ $index }})" x-on:click="open = false"
                        class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-3 pr-9"
                        id="listbox-option-0" role="option">
                        <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                        <span class="font-normal block truncate"> {{ $label }} </span>
                        @if ($selectedPeriod === $index)
                            <span
                                class="text-indigo-600 hover:text-white absolute inset-y-0 right-0 flex items-center pr-4">
                                <!-- Heroicon name: solid/check -->
                                <svg class="h-5 w-5 hover:text-white" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <canvas id="analytics"></canvas>
</div>

@once
    @push('scripts')
        <script src="{{ asset('js/chart.js') }}"></script>
    @endpush
@endOnce
