<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Schema::table('channels', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->after('link')->nullable();
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        }); 

        try {
            Artisan::call('db:seed', [
                '--class' => channelCategoriesTableSeeder::class
            ]);
        } catch (\Exception $exception) {
            echo $e->getMessage() . PHP_EOL;
        }*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            /* $table->dropForeign('channels_category_id_foreign');
            $table->dropIndex('channels_category_id_foreign');
            $table->dropColumn('category_id'); */
        });
    }
}
