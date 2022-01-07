<?php

declare(strict_types=1);

use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class EnclosureUrlTest.
 *
 * @category Podmytube
 *
 * @author   Frederik Tyteca <frederick@podmytube.com>
 *
 * @internal
 * @coversNothing
 */
class PeriodsHelperTest extends TestCase
{
    /** @test */
    public function invalid_month_should_fail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PeriodsHelper::create(-1);
    }

    /** @test */
    public function invalid_year_should_fail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PeriodsHelper::create(1, 1950);
    }

    /** @test */
    public function current_month_start_date_is_last_month_last_day(): void
    {
        $this->assertEquals(
            Carbon::createMidnightDate(date('Y'), date('m'), 1),
            PeriodsHelper::create()->startDate()
        );
    }

    /** @test */
    public function current_month_end_date_is_today(): void
    {
        $this->assertEquals(
            Carbon::today()->endOfDay(),
            PeriodsHelper::create(intval(date('m')), intval(date('Y')))->endDate()
        );
    }

    /** @test */
    public function specific_month_dates_are_previous_month_last_day_and_last_day_of_month(): void
    {
        $month = 4;
        $year = 2019;
        $periodObj = PeriodsHelper::create($month, $year);
        $this->assertEquals(
            Carbon::createMidnightDate($year, $month, 1),
            $periodObj->startDate()
        );

        $this->assertEquals(
            Carbon::createMidnightDate($year, $month, 1)->endOfMonth(),
            $periodObj->endDate()
        );
    }
}
