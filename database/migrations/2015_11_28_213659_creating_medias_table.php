<?php

use App\Modules\ForeignKeys;
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
                $table->mediumText('description'); //'description' mediumtext COLLATE utf8mb4_unicode_ci,
                $table->unsignedInteger('length'); //'length' int(10) UNSIGNED NOT NULL DEFAULT '0',
                $table->unsignedSmallInteger('duration'); //'duration' smallint(5) UNSIGNED DEFAULT NULL,
                $table->dateTime('published_at'); //'published_at' datetime DEFAULT NULL,
                $table->dateTime('grabbed_at'); //'grabbed_at' datetime DEFAULT NULL,
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
        $foreignKeys = ForeignKeys::create('medias')->get();
        if ($foreignKeys->count()) {
            foreach ($foreignKeys as $foreignKey) {
                Schema::table($foreignKey->TABLE_NAME, function (Blueprint $table) use ($foreignKeyName) {
                    $table->dropForeign($foreignKeyName);
                });
            }
        }
        Schema::dropIfExists('medias');
    }
}
