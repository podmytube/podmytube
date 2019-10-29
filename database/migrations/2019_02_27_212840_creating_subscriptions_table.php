<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingSubscriptionsTable extends Migration
{
    const _DEFAULT_PLAN_ID_IS_WEEKLY_YOUTUBER_9_MONTH = 4;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * id 
         * channel_id
         * plan_id
         * trial_ends_at
         * ends_at
         * created_at
         * updated_at
         */
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->unsignedSmallInteger('id')->autoIncrement();
                $table->string('channel_id');
                $table->unsignedTinyInteger('plan_id')->default(self::_DEFAULT_PLAN_ID_IS_WEEKLY_YOUTUBER_9_MONTH);
                $table->date('trial_ends_at')->nullable();
                $table->date('ends_at')->nullable();
                $table->timestamps(); //created_at && updated_at

                /**
                 * One channel may have only one subscription
                 */
                $table->unique('channel_id');

                /**
                 * If channels.channel_id is deleted we are removing subscription too
                 */
                $table->foreign('channel_id')
                    ->references('channel_id')->on('channels')
                    ->onDelete('cascade');
                /**
                 * If plan.id is changing we are updating subscription.plan_id
                 */
                $table->foreign('plan_id')
                    ->references('id')->on('plans')
                    //->onDelete('null')
                    ->onUpdate('cascade');
            });
        }

        Artisan::call('db:seed', [
            '--class' => subscriptionTableSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
