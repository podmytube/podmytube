<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('medias')) {
            Schema::create('medias', function (Blueprint $table) {
                $table->collation = 'utf8mb4_unicode_ci';
                $table->string('media_id'); //'media_id' varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                $table->string('channel_id'); //'channel_id' varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                $table->string('title')->nullable(); //'title' varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                $table->mediumText('description')->nullable(); //'description' mediumtext COLLATE utf8mb4_unicode_ci,
                $table->unsignedInteger('length')->default(0); //'length' int(10) UNSIGNED NOT NULL DEFAULT '0',
                $table->unsignedSmallInteger('duration')->default(0); //'duration' smallint(5) UNSIGNED DEFAULT NULL,
                $table->dateTime('published_at')->nullable(); //'published_at' datetime DEFAULT NULL,
                $table->dateTime('grabbed_at')->nullable(); //'grabbed_at' datetime DEFAULT NULL,
                $table->boolean('active')->default(true); //'active' tinyint(1) UNSIGNED DEFAULT '1',
                $table->timestamp('created_at')->nullable(); //'created_at' timestamp NULL DEFAULT NULL,
                $table->timestamp('updated_at')->nullable(); //'updated_at' timestamp NULL DEFAULT NULL

                $table->primary('media_id');

                $table->foreign('channel_id')
                    ->references('channel_id')->on('channels')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
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
    }
}
