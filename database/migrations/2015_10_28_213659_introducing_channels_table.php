<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IntroducingChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('channels')) {
            Schema::create('channels', function (Blueprint $table) {
                $table->collation = 'utf8mb4_unicode_ci';
                $table->string('channel_id'); // 'channel_id' varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                $table->integer('user_id')->unsigned()->comment('the owner user_id'); //user_id' int(10) UNSIGNED NOT NULL COMMENT 'the owner user_id',
                $table->string('channel_name')->nullable(); //channel_name' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('podcast_title')->nullable(); //podcast_title' varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('podcast_copyright')->nullable(); //podcast_copyright' varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('authors')->nullable(); //authors' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('email')->nullable(); //email' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->mediumText('description'); //description' mediumtext COLLATE utf8mb4_unicode_ci,
                $table->string('link')->nullable(); //link' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->integer('category_id')->unsigned()->nullable(); //category_id' int(10) UNSIGNED DEFAULT NULL,
                $table->string('lang', 5)->default('FR'); //lang' varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT 'FR',
                $table->boolean('explicit')->default(false); //explicit' tinyint(1) UNSIGNED DEFAULT '0',
                $table->integer('youtube_videos_count')->unsigned(); //youtube_videos_count' int(10) UNSIGNED DEFAULT '0',
                $table->integer('youtube_views_count')->unsigned()->nullable(); //youtube_views_count' int(10) UNSIGNED DEFAULT NULL,
                $table->integer('youtube_subscribers_count')->unsigned()->nullable(); //youtube_subscribers_count' int(10) UNSIGNED DEFAULT NULL,
                $table->boolean('active')->default(true); //active' tinyint(1) UNSIGNED DEFAULT '1',
                $table->boolean('valid')->default(true)->comment('is this a valid channel'); //valid' tinyint(1) NOT NULL DEFAULT '1' COMMENT 'is this a valid channel',
                $table->boolean('channel_premium')->default(false); //channel_premium' tinyint(2) UNSIGNED DEFAULT '0',
                $table->dateTime('channel_createdAt')->nullable(); //channel_createdAt' datetime DEFAULT NULL,
                $table->dateTime('channel_updatedAt')->nullable(); //channel_updatedAt' datetime DEFAULT NULL,
                $table->dateTime('podcast_updatedAt')->nullable(); //podcast_updatedAt' datetime DEFAULT NULL,
                $table->date('reject_video_too_old')->nullable(); //reject_video_too_old' date DEFAULT NULL,
                $table->string('reject_video_by_keyword')->nullable(); //reject_video_by_keyword' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('accept_video_by_tag')->nullable(); //accept_video_by_tag' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('ftp_host')->nullable(); //ftp_host' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('ftp_user')->nullable(); //ftp_user' varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('ftp_pass')->nullable(); //ftp_pass' varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->string('ftp_podcast')->default('podcast.xml'); //ftp_podcast' varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'podcast.xml',
                $table->string('ftp_dir')->nullable(); //ftp_dir' varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->boolean('ftp_pasv')->default(false); //ftp_pasv' tinyint(1) NOT NULL DEFAULT '0'

                $table->primary('channel_id');

                $table->foreign('user_id')
                    ->references('user_id')->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            1;//$table->dropForeign('users_channel_id_foreign');
        });
        Schema::dropIfExists('channels');
    }
}
