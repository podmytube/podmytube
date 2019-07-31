<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('channel_id');
            $table->unsignedInteger('category_id');
            $table->timestamps();

            /**
             * One channel may have only one category/subcat (for the moment - fred 31/07/2019)
             */
            $table->unique('channel_id');

            /**
             * If channels.channel_id is deleted we are removing channel categories too
             */
            $table->foreign('channel_id')
                ->references('channel_id')->on('channels')
                ->onDelete('cascade');

            /**
             * If category.id is deleted we are removing channel categories too
             */
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_categories');
    }
}
