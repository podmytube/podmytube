<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToThumbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thumbs', function (Blueprint $table) {
            /**
             * Creating thumbs_channel_id_foreign
             * Removing inconsistencies before
             * delete from thumbs where channel_id not in (select channel_id from channels);
             */
            $table->foreign('channel_id')
                ->references('channel_id')->on('channels')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thumbs', function (Blueprint $table) {
            $table->dropForeign('thumbs_channel_id_foreign');
        });
    }
}
