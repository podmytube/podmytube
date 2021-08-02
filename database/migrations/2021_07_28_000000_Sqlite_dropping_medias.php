<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SqliteDroppingMedias extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'sqlite') {
            Schema::dropIfExists('medias');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
