<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RemoveDeprecatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Part of the analytics (has been removed)
         */
        array_map(function ($tableName) {
            Schema::dropIfExists($tableName);
        }, ['app_stats', 'feed_stats', 'medias_stats', 'uas']);
        
        /**
         * part of reverse
         */
        array_map(function ($tableName) {
            Schema::dropIfExists($tableName);
        }, ['feeds_drawtext', 'drawtext', 'feedsToken', 'items']);

        /**
         * replaced by subscriptions
         */
        Schema::dropIfExists('contracts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
