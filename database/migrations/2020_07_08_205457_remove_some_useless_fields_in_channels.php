<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSomeUselessFieldsInChannels extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * dropping columns  1 by 1 because of sqlite (testing db).
         */
        array_map(
            function ($columnToRemove) {
                if (Schema::hasColumn('channels', $columnToRemove)) {
                    Schema::table('channels', function (Blueprint $table) use ($columnToRemove) {
                        $table->dropColumn($columnToRemove);
                    });
                }
            },
            [
                'channel_premium', 'valid',
                'youtube_videos_count', 'youtube_views_count', 'youtube_subscribers_count',
                'ftp_host', 'ftp_user', 'ftp_pass', 'ftp_podcast', 'ftp_dir', 'ftp_pasv'
            ]
        );
    }
}
