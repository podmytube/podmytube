<?php
/**
 * This class will be used (for a short time) to convert old
 * channel premium registration to subscription model registration
 */
namespace App\Services;

use App\Categories;
use App\Channel;
use App\ChannelCategories;

/**
 * This class will be used (for a short time) to convert old
 * channel premium registration to subscription model registration
 */
class CategoryMigrationService
{

    /**
     * This function will create one channelCategories model for one channel according to its category.
     *
     * @param object App\Channel model $channel the channel to convert
     * @return void
     */
    public static function transform(Channel $channel)
    {

        try {
            /**
             * Getting category that fit to the old one
             */
            $newCategoryId = self::getVersion2019Category($channel);

            /**
             * Insert one row in channelCategories
             */
            ChannelCategories::insert([
                'channel_id' => $channel->channel_id,
                'category_id' => $newCategoryId,                
            ]);

        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    /**
     * This function will return the next plan id for the channel specified.
     * @param object App\Channel $channel model object
     * @return integer newPlanId
     */
    protected static function getVersion2019Category(Channel $channel)
    {
        switch ($channel->category) {
            case "Arts":$categoryId = Categories::where("name", "arts")->first()->id;
                break;
            case "Business":$categoryId = Categories::where("name", "business")->first()->id;
                break;
            case "Comedy":$categoryId = Categories::where("name", "comedy")->first()->id;
                break;
            case "Education":$categoryId = Categories::where("name", "education")->first()->id;
                break;
            case "Games &amp; Hobbies":$categoryId = Categories::where("name", "games")->first()->id;
                break;
            case "Health":$categoryId = Categories::where("name", "healthFitness")->first()->id;
                break;
            case "Music":$categoryId = Categories::where("name", "music")->first()->id;
                break;
            case "News &amp; Politics":
            case "News & Politics":
                $categoryId = Categories::where("name", "news")->first()->id;
                break;
            case "Religion &amp; Spirituality":$categoryId = Categories::where("name", "religionSpirituality")->first()->id;
                break;
            case "Science &amp; Medicine":$categoryId = Categories::where("name", "science")->first()->id;
                break;
            case "Technology":$categoryId = Categories::where("name", "technology")->first()->id;
                break;
            case "TV &amp; Film":$categoryId = Categories::where("name", "tvFilm")->first()->id;
                break;
            case "Society &amp; Culture":
            case "Society & Culture":
                $categoryId = Categories::where("name", "societyCulture")->first()->id;
                break;
            case "Sports &amp; Recreation":$categoryId = Categories::where("name", "sports")->first()->id;
                break;
        }

        return $categoryId;
    }
}
