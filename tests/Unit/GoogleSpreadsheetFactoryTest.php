<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Factories\GoogleSpreadsheetFactory;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class GoogleSpreadsheetFactoryTest extends TestCase
{
    public const TEST_SPREADSHEET_ID = '19zavWJYIkjzXNe6XP0FWiu58VsUEnFJUHqaqx6iO-6U';

    /** @test */
    public function get_range_is_working_fine(): void
    {
        $expectedResult = [
            ['Lorem', 'Ipsum', 'Dolore', 'Sit amet'],
            ['Chamois', 'Poule', 'Truite', 'Tétard'],
        ];
        $values = GoogleSpreadsheetFactory::forSpreadsheetId(self::TEST_SPREADSHEET_ID)
            ->getRange('A1:D')
        ;
        $this->assertNotNull($values);
        $this->assertIsArray($values);
        $this->assertCount(count($expectedResult), $values);
        $this->assertEqualsCanonicalizing($expectedResult, $values);
    }

    /** @test */
    public function update_range_is_working_fine(): void
    {
        $dataToWrite = [
            ['Lorem', 'Ipsum', 'Dolore', 'Sit amet'],
            ['Chamois', 'Poule', 'Truite', 'Tétard'],
        ];
        $result = GoogleSpreadsheetFactory::forSpreadsheetId(self::TEST_SPREADSHEET_ID)
            ->updateRange('F1:I', $dataToWrite)
        ;

        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals(8, $result);
    }
}
