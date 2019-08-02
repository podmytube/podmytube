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
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        /**
         * Parents categories
         */
        Category::insert(['parent_id' => 0, 'name' => 'arts']);
        Category::insert(['parent_id' => 0, 'name' => 'business']);
        Category::insert(['parent_id' => 0, 'name' => 'comedy']);
        Category::insert(['parent_id' => 0, 'name' => 'education']);
        Category::insert(['parent_id' => 0, 'name' => 'fiction']);
        Category::insert(['parent_id' => 0, 'name' => 'government']);
        Category::insert(['parent_id' => 0, 'name' => 'history']);
        Category::insert(['parent_id' => 0, 'name' => 'healthFitness']);
        Category::insert(['parent_id' => 0, 'name' => 'kidsFamily']);
        Category::insert(['parent_id' => 0, 'name' => 'leisure']);
        Category::insert(['parent_id' => 0, 'name' => 'music']);
        Category::insert(['parent_id' => 0, 'name' => 'news']);
        Category::insert(['parent_id' => 0, 'name' => 'religionSpirituality']);
        Category::insert(['parent_id' => 0, 'name' => 'science']);
        Category::insert(['parent_id' => 0, 'name' => 'societyCulture']);
        Category::insert(['parent_id' => 0, 'name' => 'sports']);
        Category::insert(['parent_id' => 0, 'name' => 'technology']);
        Category::insert(['parent_id' => 0, 'name' => 'trueCrime']);
        Category::insert(['parent_id' => 0, 'name' => 'tvFilm']);

        /**
         * Arts categories
         */
        $parentId = Category::where("name", "arts")->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'books']);
        Category::insert(['parent_id' => $parentId, 'name' => 'design']);
        Category::insert(['parent_id' => $parentId, 'name' => 'fashionAndBeauty']);
        Category::insert(['parent_id' => $parentId, 'name' => 'food']);
        Category::insert(['parent_id' => $parentId, 'name' => 'performingArts']);
        Category::insert(['parent_id' => $parentId, 'name' => 'visualArts']);

        /**
         * Business categories
         */
        $parentId = Category::where("name", "business")->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'careers']);
        Category::insert(['parent_id' => $parentId, 'name' => 'entrepreneurShip']);
        Category::insert(['parent_id' => $parentId, 'name' => 'investing']);
        Category::insert(['parent_id' => $parentId, 'name' => 'management']);
        Category::insert(['parent_id' => $parentId, 'name' => 'marketing']);
        Category::insert(['parent_id' => $parentId, 'name' => 'nonProfit']);

        /**
         * Comedy categories
         */
        $parentId = Category::where("name", "comedy")->first()->id;
        /** no subcategories */

        /**
         * Education categories
         */
        $parentId = Category::where('name', 'education')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'courses']);
        Category::insert(['parent_id' => $parentId, 'name' => 'howTo']);
        Category::insert(['parent_id' => $parentId, 'name' => 'languageLearning']);
        Category::insert(['parent_id' => $parentId, 'name' => 'selfImprovement']);

        /**
         * Fiction categories
         */
        $parentId = Category::where('name', 'fiction')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'comedyFiction']);
        Category::insert(['parent_id' => $parentId, 'name' => 'drama']);
        Category::insert(['parent_id' => $parentId, 'name' => 'scienceFiction']);

        /**
         * Government categories
         */
        $parentId = Category::where('name', 'government')->first()->id;
        /** no subcategories */

        /**
         * History categories
         */
        $parentId = Category::where('name', 'history')->first()->id;
        /** no subcategories */

        /**
         * Health & Fitness categories
         */
        $parentId = Category::where('name', 'healthFitness')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'alternativeHealth']);
        Category::insert(['parent_id' => $parentId, 'name' => 'fitness']);
        Category::insert(['parent_id' => $parentId, 'name' => 'medicine']);
        Category::insert(['parent_id' => $parentId, 'name' => 'mentalHealth']);
        Category::insert(['parent_id' => $parentId, 'name' => 'nutrition']);
        Category::insert(['parent_id' => $parentId, 'name' => 'sexuality']);

        /**
         * Kids & Family categories
         */
        $parentId = Category::where('name', 'kidsFamily')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'educationForKids']);
        Category::insert(['parent_id' => $parentId, 'name' => 'parenting']);
        Category::insert(['parent_id' => $parentId, 'name' => 'petsAnimals']);
        Category::insert(['parent_id' => $parentId, 'name' => 'storiesForKids']);

        /**
         * Leisure categories
         */
        $parentId = Category::where('name', 'leisure')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'animationManga']);
        Category::insert(['parent_id' => $parentId, 'name' => 'automotive']);
        Category::insert(['parent_id' => $parentId, 'name' => 'aviation']);
        Category::insert(['parent_id' => $parentId, 'name' => 'crafts']);
        Category::insert(['parent_id' => $parentId, 'name' => 'games']);
        Category::insert(['parent_id' => $parentId, 'name' => 'hobbies']);
        Category::insert(['parent_id' => $parentId, 'name' => 'homeGarden']);
        Category::insert(['parent_id' => $parentId, 'name' => 'videoGames']);

        /**
         * Music categories
         */
        $parentId = Category::where('name', 'music')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'musicCommentary']);
        Category::insert(['parent_id' => $parentId, 'name' => 'musicHistory']);
        Category::insert(['parent_id' => $parentId, 'name' => 'musicInterviews']);
        
        /**
         * News categories
         */
        $parentId = Category::where('name', 'news')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'businessNews']);
        Category::insert(['parent_id' => $parentId, 'name' => 'dailyNews']);
        Category::insert(['parent_id' => $parentId, 'name' => 'entertainmentNews']);
        Category::insert(['parent_id' => $parentId, 'name' => 'newsCommentary']);
        Category::insert(['parent_id' => $parentId, 'name' => 'politics']);
        Category::insert(['parent_id' => $parentId, 'name' => 'sportsNews']);
        Category::insert(['parent_id' => $parentId, 'name' => 'techNews']);
        

        /**
         * Religion & Spirtuality categories
         */
        $parentId = Category::where('name', 'religionSpirituality')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'buddhism']);
        Category::insert(['parent_id' => $parentId, 'name' => 'christianity']);
        Category::insert(['parent_id' => $parentId, 'name' => 'hinduism']);
        Category::insert(['parent_id' => $parentId, 'name' => 'islam']);
        Category::insert(['parent_id' => $parentId, 'name' => 'judaism']);
        Category::insert(['parent_id' => $parentId, 'name' => 'religion']);
        Category::insert(['parent_id' => $parentId, 'name' => 'spirituality']);
        
        /**
         * Science categories
         */
        $parentId = Category::where('name', 'science')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'astronomy']);
        Category::insert(['parent_id' => $parentId, 'name' => 'chemistry']);
        Category::insert(['parent_id' => $parentId, 'name' => 'earthSciences']);
        Category::insert(['parent_id' => $parentId, 'name' => 'lifeSciences']);
        Category::insert(['parent_id' => $parentId, 'name' => 'mathematics']);
        Category::insert(['parent_id' => $parentId, 'name' => 'naturalSciences']);
        Category::insert(['parent_id' => $parentId, 'name' => 'nature']);
        Category::insert(['parent_id' => $parentId, 'name' => 'physics']);
        Category::insert(['parent_id' => $parentId, 'name' => 'socialSciences']);
        

        /**
         * Society & Culture categories
         */
        $parentId = Category::where('name', 'societyCulture')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'documentary']);
        Category::insert(['parent_id' => $parentId, 'name' => 'personalJournals']);
        Category::insert(['parent_id' => $parentId, 'name' => 'philosophy']);
        Category::insert(['parent_id' => $parentId, 'name' => 'placesTravel']);
        Category::insert(['parent_id' => $parentId, 'name' => 'relationships']);
        
        /**
         * Sports categories
         */
        $parentId = Category::where('name', 'sports')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'baseball']);
        Category::insert(['parent_id' => $parentId, 'name' => 'basketball']);
        Category::insert(['parent_id' => $parentId, 'name' => 'cricket']);
        Category::insert(['parent_id' => $parentId, 'name' => 'fantasySports']);
        Category::insert(['parent_id' => $parentId, 'name' => 'football']);
        Category::insert(['parent_id' => $parentId, 'name' => 'golf']);
        Category::insert(['parent_id' => $parentId, 'name' => 'hockey']);
        Category::insert(['parent_id' => $parentId, 'name' => 'rugby']);
        Category::insert(['parent_id' => $parentId, 'name' => 'running']);
        Category::insert(['parent_id' => $parentId, 'name' => 'soccer']);
        Category::insert(['parent_id' => $parentId, 'name' => 'swimming']);
        Category::insert(['parent_id' => $parentId, 'name' => 'tennis']);
        Category::insert(['parent_id' => $parentId, 'name' => 'volleyball']);
        Category::insert(['parent_id' => $parentId, 'name' => 'wilderness']);
        Category::insert(['parent_id' => $parentId, 'name' => 'wrestling']);
        
        /**
         * Techology categories
         */
        $parentId = Category::where('name', 'technology')->first()->id;
        /** no subcategories */

        /**
         * True Crime categories
         */
        $parentId = Category::where('name', 'trueCrime')->first()->id;
        /** no subcategories */

        /**
         * TV & Film categories
         */
        $parentId = Category::where('name', 'tvFilm')->first()->id;
        Category::insert(['parent_id' => $parentId, 'name' => 'afterShows']);
        Category::insert(['parent_id' => $parentId, 'name' => 'filmHistory']);
        Category::insert(['parent_id' => $parentId, 'name' => 'filmInterviews']);
        Category::insert(['parent_id' => $parentId, 'name' => 'filmReviews']);
        Category::insert(['parent_id' => $parentId, 'name' => 'tvReviews']);
    }
}
