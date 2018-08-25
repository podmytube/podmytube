<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThumbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thumbs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_id', 64)->unique();
            $table->string('file_name', 60);
            $table->string('file_disk', 10);
            $table->unsignedInteger('file_size')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thumbs');
    }
}
