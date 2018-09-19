<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InstallingStripeRequisites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'stripe_id')) {
            Schema::table('users', function ($table) {
                $table->string('stripe_id')->nullable();
                $table->string('card_brand')->nullable();
                $table->string('card_last_four')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
            });
        }

        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->string('name');
                $table->string('stripe_id');
                $table->string('stripe_plan');
                $table->integer('quantity');
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
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
        //removing subscriptions
        Schema::dropIfExists('subscriptions');

        // removing added columns to users table
        if (Schema::hasColumn('users', 'stripe_id')) {

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('stripe_id');
                $table->dropColumn('card_brand');
                $table->dropColumn('card_last_four');
                $table->dropColumn('trial_ends_at');
            });

        }

    }
}
