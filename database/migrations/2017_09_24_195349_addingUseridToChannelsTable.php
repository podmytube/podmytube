<?php
/**
 * laravel database migration : adding the userid to the channel table
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 * @package PodMyTube\Dashboard\Migrations
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * laravel database migration : adding the userid to the channel table
 */
class AddingUseridToChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {

          $table->integer('user_id')->after('channel_id')->unsigned()->comment('the owner user_id');

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
            $table->dropColumn('user_id');
        });
    }
}
