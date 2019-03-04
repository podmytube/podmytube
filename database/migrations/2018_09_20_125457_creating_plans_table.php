<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->autoIncrement();
            $table->string('name',100);
            $table->string('stripe_id',50)->nullable();
            $table->unsignedTinyInteger('price');
            $table->unsignedTinyInteger('billing_yearly')->default(false);
            $table->unsignedTinyInteger('nb_episodes_per_month');
            $table->timestamps();
        });
        
        Artisan::call('db:seed', [
            '--class' => plansTableSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
