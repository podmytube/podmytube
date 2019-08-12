<?php

namespace Tests\Unit;

use App\Category;
use illuminate\Support\Collection;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    protected static $artsCategoryId = 1;
    protected static $artsCategoryChildren;
    protected static $musicCategoryId = 11;
    protected static $musicCategoryChildren;

    public function setUp(): void
    {
        self::$artsCategoryChildren = collect([
            ["id" => 20, "name" => "books"],
            ["id" => 21, "name" => "design"],
            ["id" => 22, "name" => "fashionAndBeauty"],
            ["id" => 23, "name" => "food"],
            ["id" => 24, "name" => "performingArts"],
            ["id" => 25, "name" => "visualArts"],
        ]);

        self::$musicCategoryChildren = collect([
            ["id" => 57, "name" => "musicCommentary"],
            ["id" => 58, "name" => "musicHistory"],
            ["id" => 59, "name" => "musicInterviews"],
        ]);

        parent::setUp();
    }

    public function testListingCategories()
    {
        $expectedCategory = Category::find(self::$artsCategoryId);
        $categoriesList = Category::list();
        $wantedCategories = [self::$artsCategoryId, self::$musicCategoryId];
        
        $results = $categoriesList->filter(function ($item) use ($wantedCategories) {
            if (in_array($item->id, $wantedCategories)) {
                return $item;
            }
        });

        dd($results->pluck('name'));

        $this->assertEqualsCanonicalizing(
            self::$artsCategoryChildren->pluck('name'),
            $results->children->pluck('name')
        );
        
    }

    /**
     * Testing that the parent relationShip is working fine.
     * food (id : 23) parent is arts (id : 1)
     */
    public function testSimpleParentRelationShipShouldBeOk()
    {
        $expectedCategory = Category::find(self::$artsCategoryId);
        $this->assertEquals(
            $expectedCategory->name,
            Category::find(23)->parent->name
        );
    }

    public function testGetCategoryByUnknownNameShouldReturnNullResult()
    {
        $this->assertNull(Category::byName("This category will never exist"));
    }

    public function testGetCategoryByNameShouldBeValid()
    {
        $results = Category::byName("arts");
        $expectedCategoryId = self::$artsCategoryId;
        $this->assertEquals(
            $expectedCategoryId,
            $results->id
        );

        $this->assertEqualsCanonicalizing(
            self::$artsCategoryChildren->pluck('name'),
            $results->children->pluck('name')
        );
    }

    public function testThatIGetAllCategories()
    {
        $expectedCategories = collect([
            "id" => self::$artsCategoryId,
            "name" => "arts",
            "children" => self::$artsCategoryChildren
        ]);
        $results = Category::find(1)->with('children')->first();
        $this->assertInstanceOf(Category::class, $results);
        $this->assertEquals($expectedCategories->get('id'), $results->id);
        $this->assertEquals($expectedCategories->get('name'), $results->name);
        $this->assertEqualsCanonicalizing(
            $expectedCategories->get('children')->pluck('id'),
            $results->children->map(function ($child) {
                return $child->id;
            }) // similar to $results->children->pluck('id')
        );
    }

    public function testThatIGetRightParentId()
    {
        $expectedParentCategory = Category::where('name', 'arts')->first();
        $artsSubCategories = range(20, 25);
        foreach ($artsSubCategories as $subCategoryId) {
            $subCategory = Category::find($subCategoryId);
            $this->assertEquals(
                $expectedParentCategory->id,
                $subCategory->parent_id,
                "Category id for {{$subCategory->name}} should be {{$expectedParentCategory->id}} ({{$expectedParentCategory->name}})"
            );
        }
    }

    public function testThatIGetAllParentsId()
    {
        $expectedCategoriesId = range(1, 19);
        $categories = Category::where('parent_id', 0)->pluck('id')->toArray();
        $this->assertEquals(
            $expectedCategoriesId,
            $categories
        );
    }

    public function testThatIGotArtsCategoryIdProperly()
    {
        $this->assertEquals(
            1,
            Category::where('name', 'arts')->first()->id,
            "Arts category should be {{" . self::$artsCategoryId . "}}, was {{Category::where('name','arts')->first()->id}}"
        );
    }
}
