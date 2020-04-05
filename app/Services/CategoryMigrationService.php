<?php

/**
 * This class will be used (for a short time) to convert old
 * channel premium registration to subscription model registration
 */

namespace App\Services;

use App\Category;
use App\Channel;
use App\Exceptions\CategoryIsEmptyException;
use App\Exceptions\CategoryIsUnknownException;

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
     * @return boolean mainly for tests
     */
    public static function transform(Channel $channel)
    {
        if (!isset($channel->category) || empty($channel->category)) {
            throw new CategoryIsEmptyException(
                "This category {{$channel->category}} is unknown."
            );
        }
        try {
            /**
             * Getting category that fit to the old one
             */

            $newCategoryId = self::getVersion2019Category($channel->category);

            /**
             * Insert one row in channelCategories
             */
            $channel->category_id = $newCategoryId;
            $channel->save();
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
    public static function getVersion2019Category(string $previousCategory)
    {
        switch ($previousCategory) {
            case 'Arts':
                $categoryId = Category::where('name', 'arts')->first()->id;
                break;
            case 'Business':
                $categoryId = Category::where('name', 'business')->first()->id;
                break;
            case 'Comedy':
                $categoryId = Category::where('name', 'comedy')->first()->id;
                break;
            case 'Education':
                $categoryId = Category::where('name', 'education')->first()->id;
                break;
            case 'Games &amp; Hobbies':
                $categoryId = Category::where('name', 'games')->first()->id;
                break;
            case 'Health':
                $categoryId = Category::where('name', 'healthFitness')->first()
                    ->id;
                break;
            case 'Music':
                $categoryId = Category::where('name', 'music')->first()->id;
                break;
            case 'News &amp; Politics':
            case 'News & Politics':
                $categoryId = Category::where('name', 'news')->first()->id;
                break;
            case 'Religion &amp; Spirituality':
                $categoryId = Category::where(
                    'name',
                    'religionSpirituality'
                )->first()->id;
                break;
            case 'Science &amp; Medicine':
                $categoryId = Category::where('name', 'science')->first()->id;
                break;
            case 'Technology':
                $categoryId = Category::where('name', 'technology')->first()
                    ->id;
                break;
            case 'TV &amp; Film':
                $categoryId = Category::where('name', 'tvFilm')->first()->id;
                break;
            case 'Society &amp; Culture':
            case 'Society & Culture':
                $categoryId = Category::where('name', 'societyCulture')->first()
                    ->id;
                break;
            case 'Sports &amp; Recreation':
                $categoryId = Category::where('name', 'sports')->first()->id;
                break;
            default:
                throw new CategoryIsUnknownException(
                    "This category {{$previousCategory}} is unknown."
                );
        }

        return $categoryId;
    }
}
