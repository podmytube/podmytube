<?php

namespace Tests\Unit;

use App\Category;
use Tests\TestCase;

use App\Providers\AppServiceProvider;

class CategoryModelTest extends TestCase
{
    protected static $expectedCategories;
    protected static $expectedTotalNumberOfCategories = 110;
    protected static $expectedNumberOfParentCategories = 19;
    protected static $categoriesWithoutChildren = [
        "comedy" => ["id" => 3],
        "government" => ["id" => 6],
        "history" => ["id" => 7],
        "technology" => ["id" => 17],
        "trueCrime" => ["id" => 18],
    ];

    public function setUp(): void
    {
        /**
         * small extract of the available categories
         */
        self::$expectedCategories = [
            "arts" => [
                'id' => 1,
                'name' => 'arts',
                "children" => [
                    ["id" => 20, "name" => "books"],
                    ["id" => 21, "name" => "design"],
                    ["id" => 22, "name" => "fashionAndBeauty"],
                    ["id" => 23, "name" => "food"],
                    ["id" => 24, "name" => "performingArts"],
                    ["id" => 25, "name" => "visualArts"],
                ]
            ],
            "kidsFamily" => [
                'id' => 9,
                'name' => 'kidsFamily',
                "children" => [
                    ["id" => 48, "name" => "educationForKids"],
                    ["id" => 49, "name" => "parenting"],
                    ["id" => 50, "name" => "petsAnimals"],
                    ["id" => 51, "name" => "storiesForKids"],
                ]
            ],
            "government" => [
                'id' => 6,
                'name' => 'government',
            ],
            "music" => [
                'id' => 11,
                'name' => 'music',
                "children" => [
                    ["id" => 60, "name" => "musicCommentary"],
                    ["id" => 61, "name" => "musicHistory"],
                    ["id" => 62, "name" => "musicInterviews"],
                ]
            ]
        ];

        //dd(self::$expectedCategories->contains("name", "government"));

        parent::setUp();
    }

    public function testingSomeNumberFromCategories()
    {
        /**
         * Checking we have the right number of categories and parent categories.
         */
        $this->assertEquals(self::$expectedTotalNumberOfCategories, Category::all()->count());
        $this->assertEquals(self::$expectedNumberOfParentCategories, Category::list()->count());
    }

    public function testListingCategories()
    {
        $results = Category::list();
        foreach (self::$expectedCategories as $expectedCategory) {
            $result = $results->firstWhere("id", $expectedCategory["id"]);
            $this->assertEquals($expectedCategory["id"], $result->id);
            $this->assertEquals($expectedCategory["name"], $result->name);

            if (!isset($expectedCategory['children'])) {
                /**
                 * some categories have no have no children (poors :'( ))
                 */
                $this->assertTrue(
                    in_array(
                        $result->name,
                        array_keys(self::$categoriesWithoutChildren)
                    )
                );
                continue;
            }

            /**
             * Checking names
             */
            $this->assertEqualsCanonicalizing(
                collect($expectedCategory['children'])->pluck("name")->toArray(),
                $result->children->pluck('name')->toArray()
            );

            /**
             * Checking ids
             */
            $this->assertEqualsCanonicalizing(
                collect($expectedCategory['children'])->pluck("id")->toArray(),
                $result->children->pluck('id')->toArray()
            );
        }
    }

    /**
     * Testing that the parent relationShip is working fine.
     * food (id : 23) parent is arts (id : 1)
     */
    public function testSimpleParentRelationShipShouldBeOk()
    {
        $expectedCategory = Category::find(self::$expectedCategories["arts"]["id"]);
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
        $expectedCategoryId = self::$expectedCategories["arts"]["id"];
        $this->assertEquals(
            $expectedCategoryId,
            $results->id
        );

        $this->assertEqualsCanonicalizing(
            collect(self::$expectedCategories["arts"]["children"])->pluck('name'),
            $results->children->pluck('name')
        );
    }

    public function testThatIGetAllCategories()
    {
        $results = Category::find(1)->with('children')->first();
        $this->assertInstanceOf(Category::class, $results);
        $this->assertEquals(self::$expectedCategories["arts"]['id'], $results->id);
        $this->assertEquals(self::$expectedCategories["arts"]['name'], $results->name);
        $this->assertEqualsCanonicalizing(
            collect(self::$expectedCategories["arts"]["children"])->pluck('id'),
            $results->children->map(function ($child) {
                return $child->id;
            }) // similar to $results->children->pluck('id')
        );
    }

    /**
     * Checking that the parent id of one art category is the id of the art category itself.
     */
    public function testThatIGetRightParentId()
    {
        $artsSubCategories = range(20, 25);
        foreach ($artsSubCategories as $subCategoryId) {
            $subCategory = Category::find($subCategoryId);
            $this->assertEquals(
                self::$expectedCategories["arts"]["id"],
                $subCategory->parent_id,
                "Category id for {{$subCategory->name}} should be {" . self::$expectedCategories["arts"]["id"] . "} " .
                    "was ({" . $subCategory->name . "})"
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
            self::$expectedCategories["arts"]['id'],
            Category::where('name', 'arts')->first()->id,
            "Arts category should be {{" . self::$expectedCategories["arts"]['id'] . "}}," .
                "was {{" . Category::where('name', 'arts')->first()->id . "}}"
        );
    }
}
