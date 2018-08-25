<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrimaryIndexOnAppStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('app_stats', function (Blueprint $table) {
            $table->primary(['channel_id', 'app_day', 'ua_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // `ALTER TABLE ``app_stats`` DROP PRIMARY KEY;`
        Schema::table('app_stats', function (Blueprint $table) {
            $table->dropPrimary('PRIMARY'); 
        });
    }
}
