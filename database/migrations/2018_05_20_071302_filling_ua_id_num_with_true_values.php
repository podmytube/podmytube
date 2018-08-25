<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillingUaIdNumWithTrueValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
        DB::table('app_stats')->update(['ua_id_num' => 0]);
    }
}
