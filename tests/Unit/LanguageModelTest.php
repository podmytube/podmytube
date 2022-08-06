<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LanguageModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function by_code_is_fine(): void
    {
        // no language in table
        $this->assertNull(Language::byCode('fr'));

        $expectedIsoName = 'French';
        Language::factory()->create(['code' => 'fr', 'iso_name' => $expectedIsoName]);
        $language = Language::byCode('fr');
        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals($expectedIsoName, $language->iso_name);
    }
}
