<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleaningOldCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * removing old category fields in DB
         */
        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('subcategory');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->string('category', 40)->after('category_id');
            $table->string('subcategory', 40)->after('category');
        });   
    }
}
