<?php

use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class EnclosureUrlTest
 *
 * @category Podmytube
 * @package  Podmytube
 * @author   Frederik Tyteca <frederick@podmytube.com>
 */

class PeriodsHelperTest extends TestCase
{
    public function testInvalidMonthShouldFail()
    {
        $this->expectException(\InvalidArgumentException::class);
        PeriodsHelper::create(-1);
    }

    public function testInvalidYearShouldFail()
    {
        $this->expectException(\InvalidArgumentException::class);
        PeriodsHelper::create(1, 1950);
    }

    public function testCurrentMonthStartDateIsLastMonthLastDay()
    {
        $this->assertEquals(
            Carbon::createMidnightDate(date('Y'), date('m'), 1)->subDay(),
            PeriodsHelper::create()->startDate()
        );
    }

    public function testCurrentMonthEndDateIsToday()
    {
        $this->assertEquals(
            Carbon::createMidnightDate(date('Y'), date('m'), date('d')),
            PeriodsHelper::create(date('m'), date('Y'))->endDate()
        );
    }

    public function testSpecificMonthDatesArePreviousMonthLastDayAndLastDayOfMonth()
    {
        $month = 4;
        $year = 2019;
        $periodObj = PeriodsHelper::create($month, $year);
        $this->assertEquals(
            Carbon::createMidnightDate($year, $month, 1)->subDay(),
            $periodObj->startDate()
        );

        $this->assertEquals(
            Carbon::createMidnightDate($year, $month, 1)->endOfMonth(),
            $periodObj->endDate()
        );
    }
}
