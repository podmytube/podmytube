<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripePlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Stripe has 2 plans id for each product according to if it is live or test env.
         * So this table allow us to know which is the good stripe planId to send in the form.
         */
        Schema::create('stripe_plans', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement(); // primary key
            $table->unsignedTinyInteger('plan_id'); // the internal id from the plan table
            $table->string('stripe_id', 30); // the stripe plan id (ie "plan_EfubS6xkc5amyO")
            $table->unsignedTinyInteger('is_live')->default(0); // str
            $table->timestamps(); //created_at && updated_at

            /**
             * We have only one internal plan_id by live/test mode
             */
            $table->unique(['plan_id','is_live']);

            /**
             * 
             */
            
            $table->foreign('plan_id')
                ->references('id')->on('plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');            
            
        });

        Artisan::call('db:seed', [
            '--class' => stripePlansTableSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('stripe_plans');

    }
}
