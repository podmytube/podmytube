<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingPolymorphismToThumbs extends Migration
{
    protected $tableName = 'thumbs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('coverable_type')->nullable();
            $table->string('coverable_id')->nullable();
        });

        Artisan::call('db:seed', ['--class' => 'ThumbsPolymorphismSeeder']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('coverable_type');
            $table->dropColumn('coverable_id');
        });
    }
}
