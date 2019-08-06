<?php

namespace Tests\Unit;

use App\Category;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    public function testThatIGetAllCategories()
    {
        $expectedArtsCategories = [

        ];
        $results = Category::find(1)->with('children')->get();
        foreach ($results as $parentCategory){
            

            
        }
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
