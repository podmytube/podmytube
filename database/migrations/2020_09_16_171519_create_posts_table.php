<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('wp_id');
            $table->string('author');
            $table->string('title');
            $table->string('slug');
            $table->string('featured_image')->nullable();
            $table->boolean('sticky')->default(false);
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->string('format')->default('standard');
            $table->boolean('status')->default(true);
            $table->dateTime('published_at')->default(Carbon::now());
            $table->timestamps();
            $table->unsignedSmallInteger('post_category_id')->default(1);
            $table->foreign('post_category_id')
                ->references('id')->on('post_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade')
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
}
