<?php

declare(strict_types=1);

namespace App\Modules;

use App\Helpers\NumberChecker;
use Carbon\Carbon;

class PeriodsHelper
{
    private const YEAR_MIN = 2000;
    private const YEAR_MAX = 2090;

    private const MONTH_MIN = 1;
    private const MONTH_MAX = 12;

    protected $startDate;
    protected $endDate;

    /**
     * constructor.
     *
     * @param int $month the month we want (by default, it is current one)
     * @param int $year  the year we want (by default, it is current one)
     */
    private function __construct(
        protected ?int $month = null,
        protected ?int $year = null
    ) {
        $this->month ??= intval(date('n'));
        $this->year ??= intval(date('Y'));

        NumberChecker::isBetween($this->month, self::MONTH_MIN, self::MONTH_MAX);
        NumberChecker::isBetween($this->year, self::YEAR_MIN, self::YEAR_MAX);

        $this->startDate = Carbon::createMidnightDate($this->year, $this->month, 1)->startOfMonth();
        $this->endDate = Carbon::createMidnightDate($this->year, $this->month, 1)->endOfMonth();
        if ($this->endDate->greaterThan(Carbon::today()->endOfDay())) {
            $this->endDate = Carbon::today()->endOfDay();
        }
    }

    /**
     * Create.
     *
     * @param int $month the month we want (by default, it is current one)
     * @param int $year  the year we want (by default, it is current one)
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

    public function month(): int
    {
        return $this->month;
    }

    public function year(): int
    {
        return $this->year;
    }
}
