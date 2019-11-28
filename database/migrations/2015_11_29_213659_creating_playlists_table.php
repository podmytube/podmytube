<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingPlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('playlists')) {
            Schema::create('playlists', function (Blueprint $table) {
                $table->string('playlist_id'); // ` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
                $table->string('channel_id'); // ` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                $table->string('playlist_title'); // ` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                $table->text('playlist_description')->nullable(); // ` mediumtext COLLATE utf8mb4_unicode_ci,
                $table->string('playlist_thumbnail'); // ` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                $table->dateTime('playlist_publishedAt')->nullable(); // ` datetime DEFAULT NULL,
                $table->dateTime('playlist_updatedAt')->nullable(); // ` datetime DEFAULT NULL,
                $table->boolean('playlist_active')->default(false); // ` tinyint(1) unsigned NOT NULL DEFAULT '0',
                $table->primary('playlist_id');
                $table->collation = 'utf8mb4_unicode_ci';

                $table->foreign('channel_id')
                    ->references('channel_id')->on('channels')
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
        Schema::table('channels', function (Blueprint $table) {
            $table->dropForeign('playlists_channel_id_foreign');
        });
        Schema::dropIfExists('playlists');
    }
}
