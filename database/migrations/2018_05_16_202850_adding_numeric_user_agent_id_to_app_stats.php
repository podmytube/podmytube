<?php

/**
 * Adding the ua_id_num column to the app_stats table.
 * 
 * This column will contain the numeric id of the user agent as known in the uas table.
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingNumericUserAgentIdToAppStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // creating numeric user agents id into app_stats
        if (!Schema::hasColumn('app_stats', 'ua_id_num')) {

            Schema::table('app_stats', function (Blueprint $table) {
                $table->smallInteger('ua_id_num')->unsigned()->after('app_day');   // remove old primary string index
            });

        }

        // filling this newly numeric id with the true values
        if (Schema::hasColumn('app_stats', 'ua_id_num')) {

            //update app_stats, uas set app_stats.ua_id_num = uas.id where uas.ua_id=app_stats.ua_id;
            DB::table('app_stats')->join('uas', 'app_stats.ua_id', '=', 'uas.ua_id')->update(['app_stats.ua_id_num' => DB::raw('uas.id')]);

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('app_stats', 'ua_id_num')) {

            Schema::table('app_stats', function (Blueprint $table) {
                $table->dropColumn('ua_id_num');
            });

        }
    }
}
