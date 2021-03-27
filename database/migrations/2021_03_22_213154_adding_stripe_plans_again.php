<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingStripePlansAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_plans', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement(); // primary key
            $table->unsignedTinyInteger('plan_id'); // the internal id from the plan table
            $table->boolean('is_yearly')->default(false);
            $table->string('stripe_live_id', 30); // the stripe price live id (ie "plan_EfubS6xkc5amyO")
            $table->string('stripe_test_id', 30)->nullable(); // the stripe price test id
            $table->string('comment')->nullable();

            $table->foreign('plan_id')
                ->references('id')->on('plans')
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
        //
    }
}
