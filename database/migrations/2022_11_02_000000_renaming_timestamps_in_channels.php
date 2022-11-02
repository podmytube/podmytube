<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('channels', function (Blueprint $table): void {
            $table->renameColumn('channel_createdAt', 'created_at');
        });
        Schema::table('channels', function (Blueprint $table): void {
            $table->renameColumn('channel_updatedAt', 'updated_at');
        });
        Schema::table('channels', function (Blueprint $table): void {
            $table->renameColumn('podcast_updatedAt', 'podcast_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table): void {
            $table->renameColumn('created_at', 'channel_createdAt');
        });
        Schema::table('channels', function (Blueprint $table): void {
            $table->renameColumn('updated_at', 'channel_updatedAt');
        });
        Schema::table('channels', function (Blueprint $table): void {
            $table->renameColumn('podcast_updated_at', 'podcast_updatedAt');
        });
    }
};
