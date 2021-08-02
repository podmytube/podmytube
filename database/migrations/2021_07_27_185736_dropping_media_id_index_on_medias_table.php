<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DroppingMediaIdIndexOnMediasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medias', function (Blueprint $table): void {
            if (config('database.default') !== 'sqlite') {
                $table->dropPrimary();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
