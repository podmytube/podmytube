<?php

namespace Tests\Unit;

use App\Categories;
use App\Channel;
use App\ChannelCategories;
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
        $expectedCategoryId = Categories::where("name", "news")->first()->id;
        CategoryMigrationService::transform($channel);

        $channelCategory = ChannelCategories::where("channel_id", "freeChannel")->first();
        $this->assertEquals(
            $expectedCategoryId,
            $channelCategory->category_id,
            "The expected categoryId was {{$expectedCategoryId}}, result was {{$channelCategory->category_id}}."
        );
        $channelCategory->delete();
    }

    /**
     * Checking that for one specified old category we have the expected new category id.
     */
    public function testThatWeObtainTheRightCategory()
    {
        $expectedCategories = [
            "Arts" => Categories::where("name", "arts")->first()->id,
            "Business" => Categories::where("name", "business")->first()->id,
            "Comedy" => Categories::where("name", "comedy")->first()->id,
            "Education" => Categories::where("name", "education")->first()->id,
            "Games &amp; Hobbies" => Categories::where("name", "games")->first()->id,
            "Health" => Categories::where("name", "healthFitness")->first()->id,
            "Music" => Categories::where("name", "music")->first()->id,
            "News &amp; Politics" => Categories::where("name", "news")->first()->id,
            "News & Politics" => Categories::where("name", "news")->first()->id,
            "Religion &amp; Spirituality" => Categories::where("name", "religionSpirituality")->first()->id,
            "Science &amp; Medicine" => Categories::where("name", "science")->first()->id,
            "Technology" => Categories::where("name", "technology")->first()->id,
            "TV &amp; Film" => Categories::where("name", "tvFilm")->first()->id,
            "Society &amp; Culture" => $categoryId = Categories::where("name", "societyCulture")->first()->id,
            "Society & Culture" => $categoryId = Categories::where("name", "societyCulture")->first()->id,
            "Sports &amp; Recreation" => Categories::where("name", "sports")->first()->id,
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
