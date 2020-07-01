<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStripeIdFieldInStripePlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stripe_plans', function (Blueprint $table) {
            $table->string('stripe_id', 60)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stripe_plans', function (Blueprint $table) {
            $table->string('stripe_id', 60)->change();
        });
    }
}
