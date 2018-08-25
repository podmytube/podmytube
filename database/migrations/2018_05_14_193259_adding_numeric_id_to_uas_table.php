<?php

/**
 * Adding a numeric id to the uas table.
 * 
 * this migration do the following things :
 * - drop the old primary string index on the ua_id column
 * - make the ua_id column unique
 * - add a numeric id to the uas table
 * Down side should reverrt the table to the old state
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingNumericIdToUasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('uas', 'id')) {
            Schema::table('uas', function (Blueprint $table) {
                $table->dropPrimary('ua_id');   // remove old primary string index
            });

            Schema::table('uas', function (Blueprint $table) {
                $table->smallIncrements('id')->first(); // create the numeric primary key                 
            });

            Schema::table('uas', function (Blueprint $table) {
                $table->unique('ua_id');        // keep the ua_id (the user agent id still unique)                
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
        if (Schema::hasColumn('uas', 'id')) {
            Schema::table('uas', function (Blueprint $table) {
                $table->smallInteger('id')->change();   // change the numeric autoincrement primary key to a single smallInt
                $table->dropPrimary('id');              // removing numeric autoincrement primary key
                $table->dropColumn('id');               // removing numeric column
            });

            Schema::table('uas', function (Blueprint $table) {
                $table->string('ua_id', 300)->primary()->change();    // removing unique on ua_id  (not sure it is useful)
                $table->dropUnique('uas_ua_id_unique');              // removing unique index that is created
            });

        }
    }
}
