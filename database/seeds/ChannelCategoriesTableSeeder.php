<?php

use App\Channel;
use App\Services\CategoryMigrationService;
use Illuminate\Database\Seeder;

class ChannelCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * getting channels informations
         */
        $channels = Channel::select([
            'channel_id',
            'channel_name',
            'category',
            'channel_createdAt',
        ])
            ->whereNotNull('category')
            ->get();

        /**
         * for each channel add a subscription according to its channel_premium state
         */
        foreach ($channels as $channel) {
            try {
                CategoryMigrationService::transform($channel);
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
    }
}
