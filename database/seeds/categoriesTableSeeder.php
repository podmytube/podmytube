<?php

use App\Categories;
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
        Categories::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        /**
         * Parents categories
         */
        Categories::insert(['parent_id' => 0, 'name' => 'arts']);
        Categories::insert(['parent_id' => 0, 'name' => 'business']);
        Categories::insert(['parent_id' => 0, 'name' => 'comedy']);
        Categories::insert(['parent_id' => 0, 'name' => 'education']);
        Categories::insert(['parent_id' => 0, 'name' => 'fiction']);
        Categories::insert(['parent_id' => 0, 'name' => 'government']);
        Categories::insert(['parent_id' => 0, 'name' => 'history']);
        Categories::insert(['parent_id' => 0, 'name' => 'healthFitness']);
        Categories::insert(['parent_id' => 0, 'name' => 'kidsFamily']);
        Categories::insert(['parent_id' => 0, 'name' => 'leisure']);
        Categories::insert(['parent_id' => 0, 'name' => 'music']);
        Categories::insert(['parent_id' => 0, 'name' => 'news']);
        Categories::insert(['parent_id' => 0, 'name' => 'religionSpirituality']);
        Categories::insert(['parent_id' => 0, 'name' => 'science']);
        Categories::insert(['parent_id' => 0, 'name' => 'societyCulture']);
        Categories::insert(['parent_id' => 0, 'name' => 'sports']);
        Categories::insert(['parent_id' => 0, 'name' => 'technology']);
        Categories::insert(['parent_id' => 0, 'name' => 'trueCrime']);
        Categories::insert(['parent_id' => 0, 'name' => 'tvFilm']);

        /**
         * Arts categories
         */
        $parentId = Categories::where("name", "arts")->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'books']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'design']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'fashionAndBeauty']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'food']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'performingArts']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'visualArts']);

        /**
         * Business categories
         */
        $parentId = Categories::where("name", "business")->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'careers']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'entrepreneurShip']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'investing']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'management']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'marketing']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'nonProfit']);

        /**
         * Comedy categories
         */
        $parentId = Categories::where("name", "comedy")->first()->id;
        /** no subcategories */

        /**
         * Education categories
         */
        $parentId = Categories::where('name', 'education')->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'courses']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'howTo']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'languageLearning']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'selfImprovement']);

        /**
         * Fiction categories
         */
        $parentId = Categories::where('name', 'fiction')->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'comedyFiction']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'drama']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'scienceFiction']);

        /**
         * Government categories
         */
        $parentId = Categories::where('name', 'government')->first()->id;
        /** no subcategories */

        /**
         * History categories
         */
        $parentId = Categories::where('name', 'history')->first()->id;
        /** no subcategories */

        /**
         * Health & Fitness categories
         */
        $parentId = Categories::where('name', 'healthFitness')->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'alternativeHealth']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'fitness']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'medicine']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'mentalHealth']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'nutrition']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'sexuality']);

        /**
         * Kids & Family categories
         */
        $parentId = Categories::where('name', 'kidsFamily')->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'educationForKids']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'parenting']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'petsAnimals']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'storiesForKids']);

        /**
         * Leisure categories
         */
        $parentId = Categories::where('name', 'leisure')->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'animationManga']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'automotive']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'aviation']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'crafts']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'games']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'hobbies']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'homeGarden']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'videoGames']);

        /**
         * Music categories
         */
        $parentId = Categories::where('name', 'music')->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'musicCommentary']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'musicHistory']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'musicInterviews']);
        
        /**
         * News categories
         */
        $parentId = Categories::where('name', 'news')->first()->id;
        Categories::insert(['parent_id' => $parentId, 'name' => 'businessNews']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'dailyNews']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'entertainmentNews']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'newsCommentary']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'politics']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'sportsNews']);
        Categories::insert(['parent_id' => $parentId, 'name' => 'techNews']);
        

        /**
         * Religion & Spirtuality categories
         */
        $parentId = Categories::where('name', 'religionSpirituality')->first()->id;

        /**
         * Science categories
         */
        $parentId = Categories::where('name', 'science')->first()->id;

        /**
         * Society & Culture categories
         */
        $parentId = Categories::where('name', 'societyCulture')->first()->id;

        /**
         * Sports categories
         */
        $parentId = Categories::where('name', 'sports')->first()->id;

        /**
         * Techology categories
         */
        $parentId = Categories::where('name', 'technology')->first()->id;
        /** no subcategories */

        /**
         * True Crime categories
         */
        $parentId = Categories::where('name', 'trueCrime')->first()->id;
        /** no subcategories */

        /**
         * TV & Film categories
         */
        $parentId = Categories::where('name', 'tvFilm')->first()->id;

    }
}
