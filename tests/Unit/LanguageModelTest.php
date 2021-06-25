<?php

namespace Tests\Unit;

use App\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function by_code_is_fine()
    {
        /** no language in table */
        $this->assertNull(Language::byCode('fr'));

        /** */
        $expectedIsoName = 'French';
        factory(Language::class)->create(['code' => 'fr', 'iso_name' => $expectedIsoName]);
        $language = Language::byCode('fr');
        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals($expectedIsoName, $language->iso_name);
    }
}
