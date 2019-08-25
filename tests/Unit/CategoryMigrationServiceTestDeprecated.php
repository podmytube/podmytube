<?php

namespace Tests\Unit;

use App\Category;
use App\Channel;
use App\Services\CategoryMigrationService;
use Tests\TestCase;

class CategoryMigrationServiceTest extends TestCase
{
    /**
     * Checking that for a channel with a category we are creating the right entry into channelCategory table;
     */
    public function testThatOneChannelCategoryIsCreated()
    {
        $channel = Channel::find('freeChannel');
        $channel->category = "News &amp; Politics";
        $expectedCategoryId = Category::where("name", "news")->first()->id;
        CategoryMigrationService::transform($channel);

        $this->assertEquals(
            $expectedCategoryId,
            $channel->category_id,
            "The expected categoryId was {{$expectedCategoryId}}, result was {{$channel->category_id}}."
        );        
    }

    /**
     * Checking that for one specified old category we have the expected new category id.
     */
    public function testThatWeObtainTheRightCategory()
    {
        $expectedCategories = [
            "Arts" => Category::where("name", "arts")->first()->id,
            "Business" => Category::where("name", "business")->first()->id,
            "Comedy" => Category::where("name", "comedy")->first()->id,
            "Education" => Category::where("name", "education")->first()->id,
            "Games &amp; Hobbies" => Category::where("name", "games")->first()->id,
            "Health" => Category::where("name", "healthFitness")->first()->id,
            "Music" => Category::where("name", "music")->first()->id,
            "News &amp; Politics" => Category::where("name", "news")->first()->id,
            "News & Politics" => Category::where("name", "news")->first()->id,
            "Religion &amp; Spirituality" => Category::where("name", "religionSpirituality")->first()->id,
            "Science &amp; Medicine" => Category::where("name", "science")->first()->id,
            "Technology" => Category::where("name", "technology")->first()->id,
            "TV &amp; Film" => Category::where("name", "tvFilm")->first()->id,
            "Society &amp; Culture" => $categoryId = Category::where("name", "societyCulture")->first()->id,
            "Society & Culture" => $categoryId = Category::where("name", "societyCulture")->first()->id,
            "Sports &amp; Recreation" => Category::where("name", "sports")->first()->id,
        ];
        foreach ($expectedCategories as $oldCategory => $expectedCategoryId) {
            $result = CategoryMigrationService::getVersion2019Category($oldCategory);
            $this->assertEquals(
                $expectedCategoryId,
                $result,
                "The expected categoryId was {{$expectedCategoryId}}, result was {{$result}}."
            );
        }
    }

    /**
     * Checking that for one unknown category we are throwing one \Exception
     */
    public function testUnknownCategoryShouldBeDetected()
    {
        $this->expectException(\Exception::class);
        CategoryMigrationService::getVersion2019Category("This category won't exist ! never !");
    }

}
