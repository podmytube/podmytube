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
        Schema::create('downloads', function (Blueprint $table): void {
            $table->id();
            $table->date('log_day');
            $table->string('channel_id');
            $table->unsignedBigInteger('media_id');
            $table->unsignedBigInteger('count');
            $table->timestamps();

            $table->foreign('media_id')->references('id')->on('medias');
            $table->foreign('channel_id')->references('channel_id')->on('channels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
