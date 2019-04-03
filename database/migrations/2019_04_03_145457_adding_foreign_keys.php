<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            /**
             * Creating channels_user_id_foreign
             */
            $table->foreign('user_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });

        Schema::table('medias', function (Blueprint $table) {
            /**
             * Creating medias_channel_id_foreign
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
        Schema::table('channels', function (Blueprint $table) {
            $table->dropForeign('channels_user_id_foreign');
        });

        Schema::table('medias', function (Blueprint $table) {
            $table->dropForeign('medias_channel_id_foreign');
        });

    }
}
