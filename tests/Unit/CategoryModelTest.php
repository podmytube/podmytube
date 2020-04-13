<?php

namespace Tests\Unit;

use App\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    protected const TOTAL_NUMBER_OF_CATEGORIES_APRIL_2020 = 110;
    protected const TOTAL_NUMBER_OF_PARENT_ATEGORIES_APRIL_2020 = 19;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'CategoriesTableSeeder']);
    }

    public function testNumberOfCategoriesShouldBeGood()
    {
        /**
         * Checking we have the right number of categories and parent categories.
         */
        $this->assertEquals(
            self::TOTAL_NUMBER_OF_CATEGORIES_APRIL_2020,
            Category::all()->count()
        );
    }

    public function testNumberOfParentsCategoriesShouldBeGood()
    {
        $this->assertEquals(
            self::TOTAL_NUMBER_OF_PARENT_ATEGORIES_APRIL_2020,
            Category::where('parent_id', '=', 0)->count()
        );
    }
}
