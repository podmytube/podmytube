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
     *
     * @return bool mainly for tests
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
        } catch (\Exception $exception) {
            throw $exception;
        }
        return true;
    }

    /**
     * This function will return the next plan id for the channel specified.
     *
     * @param object App\Channel $channel model object
     *
     * @return int newPlanId
     */
    public static function getVersion2019Category(string $previousCategory)
    {
        $categoryMap = [
            'Arts' => 'arts',
            'Business' => 'Business',
            'Comedy' => 'Comedy',
            'Education' => 'Education',
            'Games &amp; Hobbies' => 'games',
            'Health' => 'healthFitness',
            'Music' => 'Music',
            'News &amp; Politics' => 'news',
            'News & Politics' => 'news',
            'Religion &amp; Spirituality' => 'religionSpirituality',
            'Science &amp; Medicine' => 'science',
            'Technology' => 'Technology',
            'TV &amp; Film' => 'tvFilm',
            'Society &amp; Culture' => 'societyCulture',
            'Society & Culture' => 'societyCulture',
            'Sports &amp; Recreation' => 'sports',
        ];

        if (!isset($categoryMap[$previousCategory])) {
            throw new CategoryIsUnknownException(
                "This category {{$previousCategory}} is unknown."
            );
        }
        $slug = $categoryMap[$previousCategory];
        return Category::where('name', $slug)->first()->id;
    }
}
