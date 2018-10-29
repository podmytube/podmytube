<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdaptingSubscription extends Migration
{
    /**
     * Run the migrations.
     * At this point the subscription table should look like that
     * id
     * user_id          // => channel_id
     * name             // to remove
     * stripe_id        // to remove ... perhaps it will be useful later
     * stripe_plan      // to remove ... perhaps it will be useful later
     * quantity         // to remove 
     * trial_ends_at    
     * ends_at     
     * created_at  
     * updated_at  
     * 
     * At this end the subscription table should be like that
     * id
     * channel_id    
     * plan_id
     * trial_ends_at
     * started_at          
     * ends_at     
     * created_at  
     * updated_at  

     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('stripe_subscriptions')) {
            
            Schema::rename('stripe_subscriptions', 'subscriptions');

        }

        Schema::table('subscriptions', function (Blueprint $table) {
            
            $table->dropColumn('user_id');
            $table->dropColumn('name');
            $table->dropColumn('stripe_id');
            $table->dropColumn('stripe_plan');
            $table->dropColumn('quantity');

            $table->string('channel_id', 64)->after('id');
            $table->unsignedTinyInteger('plan_id')->after('channel_id');
            $table->timestamp('started_at')->nullable()->after('trial_ends_at');

            $table->foreign('channel_id')->references('channel_id')->on('channels');
            $table->foreign('plan_id')->references('id')->on('plans');                    


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //$table->dropforeign('subscriptions_plan_id_foreign');
            //$table->dropforeign('subscriptions_channel_id_foreign');

            $table->dropColumn('started_at');
            $table->dropColumn('plan_id');
            $table->dropColumn('channel_id');

            $table->unsignedInteger('user_id')->after('id');
            $table->string('name')->after('user_id');
            $table->string('stripe_id')->after('name');
            $table->string('stripe_plan')->after('stripe_id');
            $table->integer('quantity')->after('stripe_plan');
            
        });
        
    }
}
