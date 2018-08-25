<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovingUaIdStrColumnAndRenamingUaIdNumToUaId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // removing old ua_id (string version) to ua_id numeric version
        if (Schema::hasColumn('app_stats', 'ua_id_num')) {

            Schema::table('app_stats', function (Blueprint $table) {
                $table->dropPrimary('PRIMARY'); 
            });

            Schema::table('app_stats', function (Blueprint $table) {
                $table->dropColumn('ua_id');
            });

            Schema::table('app_stats', function (Blueprint $table) {
                $table->renameColumn('ua_id_num', 'ua_id');
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
        if (! Schema::hasColumn('app_stats', 'ua_id_num')) {

            Schema::table('app_stats', function (Blueprint $table) {
                $table->renameColumn('ua_id', 'ua_id_num');
            });

            Schema::table('app_stats', function (Blueprint $table) {
                $table->string('ua_id', 300);
            });          

            DB::table('app_stats')->join('uas', 'app_stats.ua_id_num', '=', 'uas.ua_id')->update(['app_stats.ua_id' => DB::raw('uas.ua_id')]);

        }
    }
}
