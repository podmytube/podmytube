<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (! Schema::hasTable('app_stats')) {

			Schema::create('app_stats', function (Blueprint $table) {
				$table->string('channel_id', 64);
				$table->date('app_day'); // period to store is the day to be similar with ep dl
				$table->string('ua_id', 175);
				$table->unsignedSmallInteger('app_cpt');
				$table->primary(['channel_id', 'app_day', 'ua_id']);
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
        Schema::dropIfExists('app_stats');
    }
}
