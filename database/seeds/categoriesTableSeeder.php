<?php

use App\Category;
use Illuminate\Database\Seeder;

class categoriesTableSeeder extends Seeder
{
    
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Category::truncate();
        
        /**
         * Parents categories
         */
        $data = [
            ['parent_id' => 0, 'name' => 'arts'],
            ['parent_id' => 0, 'name' => 'business'],
            ['parent_id' => 0, 'name' => 'comedy'],
            ['parent_id' => 0, 'name' => 'education'],
            ['parent_id' => 0, 'name' => 'fiction'],
            ['parent_id' => 0, 'name' => 'government'],
            ['parent_id' => 0, 'name' => 'history'],
            ['parent_id' => 0, 'name' => 'healthFitness'],
            ['parent_id' => 0, 'name' => 'kidsFamily'],
            ['parent_id' => 0, 'name' => 'leisure'],
            ['parent_id' => 0, 'name' => 'music'],
            ['parent_id' => 0, 'name' => 'news'],
            ['parent_id' => 0, 'name' => 'religionSpirituality'],
            ['parent_id' => 0, 'name' => 'science'],
            ['parent_id' => 0, 'name' => 'societyCulture'],
            ['parent_id' => 0, 'name' => 'sports'],
            ['parent_id' => 0, 'name' => 'technology'],
            ['parent_id' => 0, 'name' => 'trueCrime'],
            ['parent_id' => 0, 'name' => 'tvFilm'],
        ];
        Category::insert($data);

        /**
         * Sub categories
         */

        $artsId = Category::where("name", "arts")->first()->id;
        $businessId = Category::where("name", "business")->first()->id;
        $comedyId = Category::where("name", "comedy")->first()->id;
        $educationId = Category::where('name', 'education')->first()->id;
        $fictionId = Category::where('name', 'fiction')->first()->id;
        $healthFitnessId = Category::where('name', 'healthFitness')->first()->id;
        $kidsFamilyId = Category::where('name', 'kidsFamily')->first()->id;
        $leisureId = Category::where('name', 'leisure')->first()->id;
        $musicId = Category::where('name', 'music')->first()->id;
        $newsId = Category::where('name', 'news')->first()->id;
        $religionSpiritualityId = Category::where('name', 'religionSpirituality')->first()->id;
        $scienceId = Category::where('name', 'science')->first()->id;
        $societyCultureId = Category::where('name', 'societyCulture')->first()->id;
        $sportsId = Category::where('name', 'sports')->first()->id;
        $tvFilmId = Category::where('name', 'tvFilm')->first()->id;

        $data = [
            /**
             * Arts categories
             */
            ['parent_id' => $artsId, 'name' => 'books'],
            ['parent_id' => $artsId, 'name' => 'design'],
            ['parent_id' => $artsId, 'name' => 'fashionAndBeauty'],
            ['parent_id' => $artsId, 'name' => 'food'],
            ['parent_id' => $artsId, 'name' => 'performingArts'],
            ['parent_id' => $artsId, 'name' => 'visualArts'],
            /**
             * Business categories
             */
            ['parent_id' => $businessId, 'name' => 'careers'],
            ['parent_id' => $businessId, 'name' => 'entrepreneurShip'],
            ['parent_id' => $businessId, 'name' => 'investing'],
            ['parent_id' => $businessId, 'name' => 'management'],
            ['parent_id' => $businessId, 'name' => 'marketing'],
            ['parent_id' => $businessId, 'name' => 'nonProfit'],
            /**
             * Comedy categories
             */
            ['parent_id' => $comedyId, 'name' => 'comedyInterviews'],
            ['parent_id' => $comedyId, 'name' => 'improv'],
            ['parent_id' => $comedyId, 'name' => 'standUp'],
            /**
             * Education categories
             */
            ['parent_id' => $educationId, 'name' => 'courses'],
            ['parent_id' => $educationId, 'name' => 'howTo'],
            ['parent_id' => $educationId, 'name' => 'languageLearning'],
            ['parent_id' => $educationId, 'name' => 'selfImprovement'],
            /**
             * Fiction categories
             */
            ['parent_id' => $fictionId, 'name' => 'comedyFiction'],
            ['parent_id' => $fictionId, 'name' => 'drama'],
            ['parent_id' => $fictionId, 'name' => 'scienceFiction'],
            /**
             * Health & Fitness categories
             */
            ['parent_id' => $healthFitnessId, 'name' => 'alternativeHealth'],
            ['parent_id' => $healthFitnessId, 'name' => 'fitness'],
            ['parent_id' => $healthFitnessId, 'name' => 'medicine'],
            ['parent_id' => $healthFitnessId, 'name' => 'mentalHealth'],
            ['parent_id' => $healthFitnessId, 'name' => 'nutrition'],
            ['parent_id' => $healthFitnessId, 'name' => 'sexuality'],
            /**
             * Kids & Family categories
             */
            ['parent_id' => $kidsFamilyId, 'name' => 'educationForKids'],
            ['parent_id' => $kidsFamilyId, 'name' => 'parenting'],
            ['parent_id' => $kidsFamilyId, 'name' => 'petsAnimals'],
            ['parent_id' => $kidsFamilyId, 'name' => 'storiesForKids'],
            /**
             * Leisure categories
             */
            ['parent_id' => $leisureId, 'name' => 'animationManga'],
            ['parent_id' => $leisureId, 'name' => 'automotive'],
            ['parent_id' => $leisureId, 'name' => 'aviation'],
            ['parent_id' => $leisureId, 'name' => 'crafts'],
            ['parent_id' => $leisureId, 'name' => 'games'],
            ['parent_id' => $leisureId, 'name' => 'hobbies'],
            ['parent_id' => $leisureId, 'name' => 'homeGarden'],
            ['parent_id' => $leisureId, 'name' => 'videoGames'],
            /**
             * Music categories
             */
            ['parent_id' => $musicId, 'name' => 'musicCommentary'],
            ['parent_id' => $musicId, 'name' => 'musicHistory'],
            ['parent_id' => $musicId, 'name' => 'musicInterviews'],
            /**
             * News categories
             */
            ['parent_id' => $newsId, 'name' => 'businessNews'],
            ['parent_id' => $newsId, 'name' => 'dailyNews'],
            ['parent_id' => $newsId, 'name' => 'entertainmentNews'],
            ['parent_id' => $newsId, 'name' => 'newsCommentary'],
            ['parent_id' => $newsId, 'name' => 'politics'],
            ['parent_id' => $newsId, 'name' => 'sportsNews'],
            ['parent_id' => $newsId, 'name' => 'techNews'],

            /**
             * Religion & Spirtuality categories
             */
            ['parent_id' => $religionSpiritualityId, 'name' => 'buddhism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'christianity'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'hinduism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'islam'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'judaism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'religion'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'spirituality'],
            /**
             * Science categories
             */
            ['parent_id' => $scienceId, 'name' => 'astronomy'],
            ['parent_id' => $scienceId, 'name' => 'chemistry'],
            ['parent_id' => $scienceId, 'name' => 'earthSciences'],
            ['parent_id' => $scienceId, 'name' => 'lifeSciences'],
            ['parent_id' => $scienceId, 'name' => 'mathematics'],
            ['parent_id' => $scienceId, 'name' => 'naturalSciences'],
            ['parent_id' => $scienceId, 'name' => 'nature'],
            ['parent_id' => $scienceId, 'name' => 'physics'],
            ['parent_id' => $scienceId, 'name' => 'socialSciences'],
            /**
             * Society & Culture categories
             */
            ['parent_id' => $societyCultureId, 'name' => 'documentary'],
            ['parent_id' => $societyCultureId, 'name' => 'personalJournals'],
            ['parent_id' => $societyCultureId, 'name' => 'philosophy'],
            ['parent_id' => $societyCultureId, 'name' => 'placesTravel'],
            ['parent_id' => $societyCultureId, 'name' => 'relationships'],
            /**
             * Sports categories
             */
            ['parent_id' => $sportsId, 'name' => 'baseball'],
            ['parent_id' => $sportsId, 'name' => 'basketball'],
            ['parent_id' => $sportsId, 'name' => 'cricket'],
            ['parent_id' => $sportsId, 'name' => 'fantasySports'],
            ['parent_id' => $sportsId, 'name' => 'football'],
            ['parent_id' => $sportsId, 'name' => 'golf'],
            ['parent_id' => $sportsId, 'name' => 'hockey'],
            ['parent_id' => $sportsId, 'name' => 'rugby'],
            ['parent_id' => $sportsId, 'name' => 'running'],
            ['parent_id' => $sportsId, 'name' => 'soccer'],
            ['parent_id' => $sportsId, 'name' => 'swimming'],
            ['parent_id' => $sportsId, 'name' => 'tennis'],
            ['parent_id' => $sportsId, 'name' => 'volleyball'],
            ['parent_id' => $sportsId, 'name' => 'wilderness'],
            ['parent_id' => $sportsId, 'name' => 'wrestling'],
            /**
             * TV & Film categories
             */
            ['parent_id' => $tvFilmId, 'name' => 'afterShows'],
            ['parent_id' => $tvFilmId, 'name' => 'filmHistory'],
            ['parent_id' => $tvFilmId, 'name' => 'filmInterviews'],
            ['parent_id' => $tvFilmId, 'name' => 'filmReviews'],
            ['parent_id' => $tvFilmId, 'name' => 'tvReviews'],
        ];
        Category::insert($data);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
