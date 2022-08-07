<?php

declare(strict_types=1);

use App\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SqliteRecreatingMedias extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'sqlite') {
            Schema::create('medias', function (Blueprint $table): void {
                $table->collation = 'utf8mb4_unicode_ci';
                $table->bigIncrements('id');
                $table->string('media_id');
                $table->string('channel_id');
                $table->string('title')->nullable();
                $table->mediumText('description')->nullable();
                $table->unsignedInteger('length')->default(0);
                $table->unsignedSmallInteger('duration')->default(0);
                $table->dateTime('published_at')->nullable();
                $table->dateTime('grabbed_at')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
                $table->boolean('uploaded_by_user')->after('duration')->default(false);
                $table->unsignedTinyInteger('status')->default(Media::STATUS_NOT_DOWNLOADED)->after('grabbed_at');

                $table->foreign('channel_id')
                    ->references('channel_id')->on('channels')
                    ->onDelete('cascade')
                    ->onUpdate('cascade')
                ;
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
