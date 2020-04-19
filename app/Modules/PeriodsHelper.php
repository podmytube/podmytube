<?php

namespace App\Modules;

use App\Helpers\NumberChecker;
use Carbon\Carbon;

class PeriodsHelper
{
    private const YEAR_MIN = 2000;
    private const YEAR_MAX = 2050;

    private const MONTH_MIN = 1;
    private const MONTH_MAX = 12;

    protected $startDate;
    protected $endDate;

    /**
     * constructor.
     *
     * @param int $month the month we want (by default, it is current one)
     * @param int $year the year we want (by default, it is current one)
     */
    private function __construct(
        ?int $monthParam = null,
        ?int $yearParam = null
    ) {
        $month = $monthParam ?? date('n');
        $year = $yearParam ?? date('Y');

        NumberChecker::isBetween($month, self::MONTH_MIN, self::MONTH_MAX);
        NumberChecker::isBetween($year, self::YEAR_MIN, self::YEAR_MAX);

        $this->startDate = Carbon::createMidnightDate(
            $year,
            $month,
            1
        )->subDay();
        $this->endDate = Carbon::createMidnightDate(
            $year,
            $month,
            1
        )->endOfMonth();
        if ($this->endDate->greaterThan(Carbon::createMidnightDate())) {
            $this->endDate = Carbon::createMidnightDate();
        }
    }

    /**
     * Create
     *
     * @param int $month the month we want (by default, it is current one)
     * @param int $year the year we want (by default, it is current one)
     */
    public static function create(?int $month = null, ?int $year = null)
    {
        return new static($month, $year);
    }

    /**
     * Getting startdate.
     *
     * @return Carbon $startDate
     */
    public function startDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * Getting enddate.
     *
     * @return Carbon $endDate
     */
    public function endDate(): Carbon
    {
        return $this->endDate;
    }
}
