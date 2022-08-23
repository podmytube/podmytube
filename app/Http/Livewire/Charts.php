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
    public array $abscissa = [];
    public array $ordinate = [];
    public int $selectedPeriod = 0;
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

        $this->emit('updateChartsData');
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
        [$startDateToKeep, $endDate] = $this->fromPeriodToDates($this->selectedPeriod);

        // filling fake downloads with 0
        $startDate = clone $startDateToKeep;
        $paddedDownloads = [];
        while ($startDate->lessThan($endDate)) {
            $paddedDownloads[$startDate->toDateString()] = 0;
            $startDate->addDay();
        }

        // getting downloads
        $downloads = Download::downloadsForChannelByDay($this->channel, $startDateToKeep, $endDate)->pluck('counted', 'log_day')->toArray();

        // merging with padded
        $downloads = array_merge($paddedDownloads, $downloads);

        // building datasets
        $this->abscissa = $this->ordinate = [];
        foreach ($downloads as $dateKey => $counted) {
            $this->abscissa[] = $dateKey;
            $this->ordinate[] = $counted;
        }
    }
}
