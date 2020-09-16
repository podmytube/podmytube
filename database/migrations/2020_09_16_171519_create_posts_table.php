<?php

use App\Post;
use App\PostCategory;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('wp_id');
            $table->unsignedTinyInteger('user_id');
            $table->string('title');
            $table->string('slug');
            $table->string('featured_image');
            $table->boolean('featured')->default(false);
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->string('format')->default('standard');
            $table->unsignedTinyInteger('status')->default(Post::STATUS_PUBLISHED);
            $table->dateTime('published_at')->default(Carbon::now());
            $table->timestamps();
            $table->unsignedTinyInteger('category_id')->default(PostCategory::NEWS);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
