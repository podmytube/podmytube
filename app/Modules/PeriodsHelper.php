<?php

namespace App\Modules;

use Carbon\Carbon;
use App\Exceptions\PeriodsHelperInvalidMonthException;
use App\Exceptions\PeriodsHelperInvalidYearException;

class PeriodsHelper
{
    private const YEAR_MIN = 2000;
    private const YEAR_MAX = 2050;

    private const MONTH_MIN = 1;
    private const MONTH_MAX = 12;

    protected $startDate = null;
    protected $endDate = null;

    /**
     * constructor.
     * 
     * @param integer $month the month we want (by default, it is current one)
     * @param integer $year the year we want (by default, it is current one)
     */
    private function __construct(int $month = null, int $year = null)
    {
        /**
         * If $month is set with negative value or value more than 12, Carbon is calculatin the month. 
         * I don't want this to happen.
         */
        if (isset($month)) {
            if (self::MONTH_MIN > $month || $month > self::MONTH_MAX) {
                throw new PeriodsHelperInvalidMonthException("Month {$month} should be set between {" . self::MONTH_MIN . "} and {" . self::MONTH_MAX . "}. ");
            }
        } else {
            $month = date('n');
        }

        if (isset($year)) {
            if (self::YEAR_MIN >= $year || $year > self::YEAR_MAX) {
                throw new PeriodsHelperInvalidYearException("Year {$year} should be set between {" . self::YEAR_MIN . "} and {" . self::YEAR_MAX . "}. ");
            }
        } else {
            $year = date('Y');
        }

        $this->startDate = Carbon::createMidnightDate($year, $month, 1)->subDay();
        $this->endDate = Carbon::createMidnightDate($year, $month, 1)->endOfMonth();

        if ($this->endDate->greaterThan(Carbon::createMidnightDate())) {
            $this->endDate = Carbon::createMidnightDate();
        }
    }

    /**
     * Create
     * 
     * @param integer $month the month we want (by default, it is current one)
     * @param integer $year the year we want (by default, it is current one)
     */
    public static function create(int $month = null, int $year = null)
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
