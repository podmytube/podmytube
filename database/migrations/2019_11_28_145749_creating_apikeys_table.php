<?php

declare(strict_types=1);

use App\Models\ApiKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatingApikeysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table): void {
            $table->smallIncrements('id');
            $table->string('apikey');
            $table->string('comment');
            $table
                ->unsignedTinyInteger('environment')
                ->default(ApiKey::PROD_ENV)
            ;
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /* Schema::table('quotas', function (Blueprint $table) {
            $table->dropForeign('quotas_apikey_id_foreign');
        }); */
        Schema::dropIfExists('api_keys');
    }
}
