<?php

declare(strict_types=1);

use App\Models\ApiKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovingEnvironmentColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table): void {
            $table->dropColumn('environment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table): void {
            $table
                ->unsignedTinyInteger('environment')
                ->default(ApiKey::PROD_ENV)
            ;
        });
    }
}
