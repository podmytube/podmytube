<?php

use App\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * I removed foreign key check to avoid losing data on channels
         * table during category slug column adding
         */

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('categories')->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            DB::table('categories')->delete();
        }
        /**
         * Parents categories
         */
        $data = [
            ['parent_id' => 0, 'name' => 'Arts'],
            ['parent_id' => 0, 'name' => 'Business'],
            ['parent_id' => 0, 'name' => 'Comedy'],
            ['parent_id' => 0, 'name' => 'Education'],
            ['parent_id' => 0, 'name' => 'Fiction'],
            ['parent_id' => 0, 'name' => 'Government'],
            ['parent_id' => 0, 'name' => 'History'],
            ['parent_id' => 0, 'name' => 'Health & Fitness'],
            ['parent_id' => 0, 'name' => 'Kids & Family'],
            ['parent_id' => 0, 'name' => 'Leisure'],
            ['parent_id' => 0, 'name' => 'Music'],
            ['parent_id' => 0, 'name' => 'News'],
            ['parent_id' => 0, 'name' => 'Religion & Spirituality'],
            ['parent_id' => 0, 'name' => 'Science'],
            ['parent_id' => 0, 'name' => 'Society & Culture'],
            ['parent_id' => 0, 'name' => 'Sports'],
            ['parent_id' => 0, 'name' => 'Technology'],
            ['parent_id' => 0, 'name' => 'True & Crime'],
            ['parent_id' => 0, 'name' => 'Tv & Film'],
        ];

        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                    'slug' => Str::slug($item['name']),
                ]);
            },
            $data
        );
        Category::insert($data);

        /**
         * Sub categories
         */

        $artsId = Category::bySlug('arts')->id;
        $businessId = Category::bySlug('business')->id;
        $comedyId = Category::bySlug('comedy')->id;
        $educationId = Category::bySlug('education')->id;
        $fictionId = Category::bySlug('fiction')->id;
        $healthFitnessId = Category::bySlug('health-fitness')->id;
        $kidsFamilyId = Category::bySlug('kids-family')->id;
        $leisureId = Category::bySlug('leisure')->id;
        $musicId = Category::bySlug('music')->id;
        $newsId = Category::bySlug('news')->id;
        $religionSpiritualityId = Category::bySlug('religion-spirituality')->id;
        $scienceId = Category::bySlug('science')->id;
        $societyCultureId = Category::bySlug('society-culture')->id;
        $sportsId = Category::bySlug('sports')->id;
        $tvFilmId = Category::bySlug('tv-film')->id;

        $data = [
            /**
             * Arts categories
             */
            ['parent_id' => $artsId, 'name' => 'Books'],
            ['parent_id' => $artsId, 'name' => 'Design'],
            ['parent_id' => $artsId, 'name' => 'Fashion & Beauty'],
            ['parent_id' => $artsId, 'name' => 'Food'],
            ['parent_id' => $artsId, 'name' => 'Performing Arts'],
            ['parent_id' => $artsId, 'name' => 'Visual Arts'],
            /**
             * Business categories
             */
            ['parent_id' => $businessId, 'name' => 'Careers'],
            ['parent_id' => $businessId, 'name' => 'Entrepreneurship'],
            ['parent_id' => $businessId, 'name' => 'Investing'],
            ['parent_id' => $businessId, 'name' => 'Management'],
            ['parent_id' => $businessId, 'name' => 'Marketing'],
            ['parent_id' => $businessId, 'name' => 'NonProfit'],
            /**
             * Comedy categories
             */
            ['parent_id' => $comedyId, 'name' => 'Comedy Interviews'],
            ['parent_id' => $comedyId, 'name' => 'Improv'],
            ['parent_id' => $comedyId, 'name' => 'StandUp'],
            /**
             * Education categories
             */
            ['parent_id' => $educationId, 'name' => 'Courses'],
            ['parent_id' => $educationId, 'name' => 'How To'],
            ['parent_id' => $educationId, 'name' => 'Language Learning'],
            ['parent_id' => $educationId, 'name' => 'Self-Improvement'],
            /**
             * Fiction categories
             */
            ['parent_id' => $fictionId, 'name' => 'Comedy Fiction'],
            ['parent_id' => $fictionId, 'name' => 'Drama'],
            ['parent_id' => $fictionId, 'name' => 'Science Fiction'],
            /**
             * Health & Fitness categories
             */
            ['parent_id' => $healthFitnessId, 'name' => 'Alternative Health'],
            ['parent_id' => $healthFitnessId, 'name' => 'Fitness'],
            ['parent_id' => $healthFitnessId, 'name' => 'Medicine'],
            ['parent_id' => $healthFitnessId, 'name' => 'Mental Health'],
            ['parent_id' => $healthFitnessId, 'name' => 'Nutrition'],
            ['parent_id' => $healthFitnessId, 'name' => 'Sexuality'],
            /**
             * Kids & Family categories
             */
            ['parent_id' => $kidsFamilyId, 'name' => 'Education for Kids'],
            ['parent_id' => $kidsFamilyId, 'name' => 'Parenting'],
            ['parent_id' => $kidsFamilyId, 'name' => 'Pets & Animals'],
            ['parent_id' => $kidsFamilyId, 'name' => 'Stories For Kids'],
            /**
             * Leisure categories
             */
            ['parent_id' => $leisureId, 'name' => 'Animation Manga'],
            ['parent_id' => $leisureId, 'name' => 'Automotive'],
            ['parent_id' => $leisureId, 'name' => 'Aviation'],
            ['parent_id' => $leisureId, 'name' => 'Crafts'],
            ['parent_id' => $leisureId, 'name' => 'Games'],
            ['parent_id' => $leisureId, 'name' => 'Hobbies'],
            ['parent_id' => $leisureId, 'name' => 'Home & Garden'],
            ['parent_id' => $leisureId, 'name' => 'Video Games'],
            /**
             * Music categories
             */
            ['parent_id' => $musicId, 'name' => 'Music Commentary'],
            ['parent_id' => $musicId, 'name' => 'Music History'],
            ['parent_id' => $musicId, 'name' => 'Music Interviews'],
            /**
             * News categories
             */
            ['parent_id' => $newsId, 'name' => 'Business News'],
            ['parent_id' => $newsId, 'name' => 'Daily News'],
            ['parent_id' => $newsId, 'name' => 'Entertainment News'],
            ['parent_id' => $newsId, 'name' => 'News Commentary'],
            ['parent_id' => $newsId, 'name' => 'Politics'],
            ['parent_id' => $newsId, 'name' => 'Sports News'],
            ['parent_id' => $newsId, 'name' => 'Tech News'],

            /**
             * Religion & Spirtuality categories
             */
            ['parent_id' => $religionSpiritualityId, 'name' => 'Buddhism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Christianity'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Hinduism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Islam'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Judaism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Religion'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Spirituality'],
            /**
             * Science categories
             */
            ['parent_id' => $scienceId, 'name' => 'Astronomy'],
            ['parent_id' => $scienceId, 'name' => 'Chemistry'],
            ['parent_id' => $scienceId, 'name' => 'Earth Sciences'],
            ['parent_id' => $scienceId, 'name' => 'Life Sciences'],
            ['parent_id' => $scienceId, 'name' => 'Mathematics'],
            ['parent_id' => $scienceId, 'name' => 'Natural Sciences'],
            ['parent_id' => $scienceId, 'name' => 'Nature'],
            ['parent_id' => $scienceId, 'name' => 'Physics'],
            ['parent_id' => $scienceId, 'name' => 'Social Sciences'],
            /**
             * Society & Culture categories
             */
            ['parent_id' => $societyCultureId, 'name' => 'Documentary'],
            ['parent_id' => $societyCultureId, 'name' => 'Personal Journals'],
            ['parent_id' => $societyCultureId, 'name' => 'Philosophy'],
            ['parent_id' => $societyCultureId, 'name' => 'Places & Travel'],
            ['parent_id' => $societyCultureId, 'name' => 'Relationships'],
            /**
             * Sports categories
             */
            ['parent_id' => $sportsId, 'name' => 'Baseball'],
            ['parent_id' => $sportsId, 'name' => 'Basketball'],
            ['parent_id' => $sportsId, 'name' => 'Cricket'],
            ['parent_id' => $sportsId, 'name' => 'Fantasy Sports'],
            ['parent_id' => $sportsId, 'name' => 'Football'],
            ['parent_id' => $sportsId, 'name' => 'Golf'],
            ['parent_id' => $sportsId, 'name' => 'Hockey'],
            ['parent_id' => $sportsId, 'name' => 'Rugby'],
            ['parent_id' => $sportsId, 'name' => 'Running'],
            ['parent_id' => $sportsId, 'name' => 'Soccer'],
            ['parent_id' => $sportsId, 'name' => 'Swimming'],
            ['parent_id' => $sportsId, 'name' => 'Tennis'],
            ['parent_id' => $sportsId, 'name' => 'Volleyball'],
            ['parent_id' => $sportsId, 'name' => 'Wilderness'],
            ['parent_id' => $sportsId, 'name' => 'Wrestling'],
            /**
             * TV & Film categories
             */
            ['parent_id' => $tvFilmId, 'name' => 'After Shows'],
            ['parent_id' => $tvFilmId, 'name' => 'Film History'],
            ['parent_id' => $tvFilmId, 'name' => 'Film Interviews'],
            ['parent_id' => $tvFilmId, 'name' => 'Film Reviews'],
            ['parent_id' => $tvFilmId, 'name' => 'Tv Reviews'],
        ];
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                    'slug' => Str::slug($item['name']),
                ]);
            },
            $data
        );
        Category::insert($data);
    }
}
