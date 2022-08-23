<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
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

        return view('analytics.show', compact('channel'));
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
