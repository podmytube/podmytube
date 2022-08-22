<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Channel;
use App\Models\Download;
use Carbon\Carbon;
use Livewire\Component;

class Charts extends Component
{
    public const PERIOD_THIS_MONTH = 0;
    public const PERIOD_THIS_WEEK = 1;
    public const PERIOD_LAST_MONTH = 2;
    public const PERIOD_LAST_WEEK = 3;

    public const DEFAULT_PERIOD = 0;

    public Channel $channel;
    public string $jsAbscissa;
    public string $jsOrdinate;
    public mixed $selectedPeriod = 0;
    public string $selectedPeriodLabel;

    public array $periods = [];

    public function mount(Channel $channel): void
    {
        $this->channel = $channel;
        $this->periods = [
            static::PERIOD_THIS_MONTH => 'This month',
            static::PERIOD_THIS_WEEK => 'This week',
            static::PERIOD_LAST_MONTH => 'Last month',
            static::PERIOD_LAST_WEEK => 'Last week',
        ];
        $this->selectedPeriodLabel = $this->periods[$this->selectedPeriod];

        $this->buildCoordinates();
    }

    public function selectingPeriod(int $index): void
    {
        $this->selectedPeriod = $index;
        $this->selectedPeriodLabel = $this->periods[$this->selectedPeriod];
        $this->buildCoordinates();

        $this->dispatchBrowserEvent('chartsDataUpdated');
    }

    public function render()
    {
        return view('livewire.charts');
    }

    /**
     * @return array<Carbon>
     */
    public function fromPeriodToDates(?int $period = null): array
    {
        return match ($period) {
            static::PERIOD_LAST_WEEK => [now()->subWeek()->startOfWeek(weekStartsAt: Carbon::MONDAY), now()->subWeek()->endOfWeek(weekEndsAt: Carbon::SUNDAY)],
            static::PERIOD_LAST_MONTH => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            static::PERIOD_THIS_WEEK => [now()->startOfWeek(weekStartsAt: Carbon::MONDAY), now()->endOfWeek(weekEndsAt: Carbon::SUNDAY)],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    public function buildCoordinates(): void
    {
        [$startDate, $endDate] = $this->fromPeriodToDates($this->selectedPeriod);

        $downloads = Download::downloadsForChannelByDay($this->channel, $startDate, $endDate);

        $abscissa = $ordinate = [];
        while ($startDate->lessThan($endDate)) {
            $abscissa[] = $startDate->format('j M');

            $result = $downloads->first(fn (Download $download) => $startDate->toDateString() === $download->log_day->toDateString());
            $ordinate[] = $result !== null ? $result->counted : 0;
            $startDate->addDay();
        }

        $this->jsAbscissa = json_encode($abscissa);
        $this->jsOrdinate = json_encode($ordinate);
    }
}
