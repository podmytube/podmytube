<?php
/**
 * laravel database migration : creating password reset table
 * 
 * This is one part of the laravel Auth out of the box features. Not really used for the moment
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 * @package PodMyTube\Dashboard\Migrations
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * laravel database migration : create the password reset table
 */
class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
}
