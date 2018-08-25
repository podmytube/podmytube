<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('media_stats')) {

            Schema::create('medias_stats', function (Blueprint $table) {
                $table->string('channel_id', 64);
                $table->string('media_id', 64);
                $table->date('media_day');
                $table->unsignedMediumInteger('media_cpt');
                $table->primary(['channel_id', 'media_id', 'media_day']);
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
        Schema::dropIfExists('medias_stats');
    }
}
