<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixStripeIdColumnNameInUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // deleting wrong column name
        if (Schema::hasColumn('users', 'stripe_id, 60')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('stripe_id, 60');
            });
        }

        // creating users.stripe_id
        if (!Schema::hasColumn('users', 'stripe_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('stripe_id', 30)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('stripe_id');
        });
    }
}
