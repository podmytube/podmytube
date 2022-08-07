<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Language;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ChannelsTableSeeder extends Seeder
{
    public const JEANVIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (!App::environment('local')) {
            return true;
        }

        DB::table('channels')->delete();

        /** create channel */
        $channel = Channel::create([
            'channel_id' => self::JEANVIET_CHANNEL_ID,
            'user_id' => User::byEmail('frederick@podmytube.com')->user_id,
            'channel_name' => 'Jean Viet',
            'podcast_title' => null,
            'podcast_copyright' => '',
            'authors' => 'Jean Baptiste Viet',
            'email' => 'jeanviet@example.com',
            'description' => 'lorem',
            'link' => 'https://www.youtube.com/channel/UCu0tUATmSnMMCbCRRYXmVlQ',
            'category_id' => Category::bySlug('education')->id, // education
            'language_id' => Language::byCode('fr')->id,
            'explicit' => false,
            'active' => true,
            'reject_video_too_old' => null,
            'reject_video_by_keyword' => null,
            'accept_video_by_tag' => null,
            'channel_createdAt' => now(),
            'channel_updatedAt' => now(),
            'podcast_updatedAt' => now(),
        ]);

        Subscription::create(
            [
                'channel_id' => $channel->channel_id,
                'plan_id' => Plan::bySlug('starter')->id,
            ]
        );
    }
}
