<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Download;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public const PERIOD_THIS_MONTH = 0;
    public const PERIOD_THIS_WEEK = 1;
    public const PERIOD_LAST_MONTH = 2;
    public const PERIOD_LAST_WEEK = 3;

    public const DEFAULT_PERIOD = 0;

    /**
     * display all informations about one channel.
     *
     * @param ChannelRequest $request
     *
     * @return Response*
     */
    public function show(Request $request, Channel $channel)
    {
        $this->authorize($channel);

        [$startDate, $endDate] = $this->fromPeriodToDates(intval($request->query('p')));

        $downloads = Download::downloadsForChannelByDay($channel, $startDate, $endDate);

        $abscissa = $ordinate = [];
        while ($startDate->lessThan($endDate)) {
            $abscissa[] = $startDate->format('j M');

            $result = $downloads->first(fn (Download $download) => $startDate->toDateString() === $download->log_day->toDateString());
            $ordinate[] = $result !== null ? $result->counted : 0;
            $startDate->addDay();
        }

        $abscissa = "['" . implode("','", $abscissa) . "']";
        $ordinate = "['" . implode("','", $ordinate) . "']";

        return view('analytics.show', compact('channel', 'downloads', 'startDate', 'endDate', 'abscissa', 'ordinate'));
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
}
