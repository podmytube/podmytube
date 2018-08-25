<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreasingAppStatsUaIdFieldFrom175To300 extends Migration
{
    /**
     * Run the migrations.
     * field ua_id is passing from string(175) to string(300)
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('app_stats')) {
            Schema::table('app_stats', function (Blueprint $table) {
                $table->string('ua_id', 300)->change();
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
        // cannot be reversed 
        // error "Warning: 1265 Data truncated for column 'ua_id'"
    }
}
