<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UniqueMediaIdOnMediasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medias', function (Blueprint $table): void {
            $table->unique('media_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
