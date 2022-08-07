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
        Schema::table('webhook_calls', function (Blueprint $table): void {
            $table->string('url')->nullable();
            $table->json('headers')->nullable();
            $table->json('payload')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhook_calls', function (Blueprint $table): void {
            $table->dropColumn('url');
            $table->dropColumn('headers');
            $table->text('payload')->change();
        });
    }
};
