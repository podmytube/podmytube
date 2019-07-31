<?php

use App\Channel;
use App\ChannelCategories;
use App\Services\CategoryMigrationService;
use Illuminate\Database\Seeder;

class channelCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment(['dev', 'local', 'rec', 'testing'])) {
            ChannelCategories::truncate();
        }

        /**
         * getting channels informations
         */
        $channels = Channel::select(['channel_id', 'channel_name', 'category', 'channel_createdAt'])
            ->whereNotNull('category')
            ->get();

        /**
         * for each channel add a subscription according to its channel_premium state
         */
        foreach ($channels as $channel) {

            try {
                CategoryMigrationService::transform($channel);
            } catch (\Exception $e) {
                die("Channel category transformation has failed with message : {{$e->getMessage()}} ");
            }
        }
    }
}
