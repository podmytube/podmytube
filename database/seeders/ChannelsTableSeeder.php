<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Language;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

class ChannelsTableSeeder extends LocalSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateTables('channels');

        // create channel jean viet
        Channel::factory()
            ->user(User::byEmail('frederick@podmytube.com'))
            ->category(Category::bySlug('education'))
            ->language(Language::byCode('fr'))
            ->has(Subscription::factory()->plan(Plan::bySlug('starter')))
            ->create([
                'channel_id' => self::JEANVIET_CHANNEL_ID,
                'channel_name' => 'Jean Viet',
                'authors' => 'Jean Baptiste Viet',
                'email' => 'jeanviet@example.com',
                'link' => 'https://www.youtube.com/channel/UCu0tUATmSnMMCbCRRYXmVlQ',
            ])
        ;

        // create another channel
        Channel::factory()
            ->user(User::byEmail('frederick@podmytube.com'))
            ->category(Category::bySlug('technology'))
            ->language(Language::byCode('fr'))
            ->has(Subscription::factory()->plan(Plan::bySlug('starter')))
            ->create([
                'channel_id' => static::FTYTECA_CHANNEL_ID,
                'channel_name' => 'Frederick Tyteca',
                'authors' => 'Frederick Tyteca',
                'email' => 'frederick@tyteca.net',
                'link' => 'https://www.tyteca.net',
            ])
        ;
    }
}
