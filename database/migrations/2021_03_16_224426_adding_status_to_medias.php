<?php

declare(strict_types=1);

use App\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingStatusToMedias extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medias', function (Blueprint $table): void {
            $table->unsignedTinyInteger('status')->default(Media::STATUS_NOT_DOWNLOADED)->after('grabbed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
