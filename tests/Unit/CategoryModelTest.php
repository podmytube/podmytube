<?php

namespace Tests\Unit;

use App\Category;
use illuminate\Support\Collection;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    protected $artsCategoryChildren;

    public function setUp(): void
    {
        $this->artsCategoryChildren = collect([
            ["id" => 20, "name" => "books"],
            ["id" => 21, "name" => "design"],
            ["id" => 22, "name" => "fashionAndBeauty"],
            ["id" => 23, "name" => "food"],
            ["id" => 24, "name" => "performingArts"],
            ["id" => 25, "name" => "visualArts"],
        ]);
        parent::setUp();
    }

    public function testListingCategories(){
        $expectedCategory = Category::find(1);
        $categoriesList = Category::list();
        
        
        
    }

    /**
     * Testing that the parent relationShip is working fine.
     * food (id : 23) parent is arts (id : 1)
     */
    public function testSimpleParentRelationShipShouldBeOk()
    {
        $expectedCategory = Category::find(1);
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
        $expectedCategoryId = 1;
        $this->assertEquals(
            $expectedCategoryId,
            $results->id
        );

        $this->assertEqualsCanonicalizing(
            $this->artsCategoryChildren->pluck('name'),
            $results->children->pluck('name')
        );
    }

    public function testThatIGetAllCategories()
    {
        $expectedCategories = collect([
            "id" => 1,
            "name" => "arts",
            "children" => $this->artsCategoryChildren
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
            "Arts category should be 1, was {{Category::where('name','arts')->first()->id}}"
        );
    }
}
