<?php

/**
 * laravel database migration : creating the users table 
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 * @package PodMyTube\Dashboard\Migrations
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * laravel database migration : create users table 
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        $foreignKeys = DB::table('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')
            ->select(
                [
                    'TABLE_NAME',
                    'COLUMN_NAME',
                    'CONSTRAINT_NAME',
                    'REFERENCED_TABLE_NAME',
                    'REFERENCED_COLUMN_NAME',
                ]
            )
            ->where([
                ['REFERENCED_TABLE_SCHEMA', env('DB_DATABASE')],
                ['REFERENCED_TABLE_NAME', 'channels']
            ])->get();
        if ($foreignKeys->count()) {
            foreach ($foreignKeys as $foreignKey) {
                Schema::table($foreignKey->TABLE_NAME, function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey->CONSTRAINT_NAME);
                });
            }
        }
        //DB::select(SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE  WHERE REFERENCED_TABLE_SCHEMA = 'pmt' AND REFERENCED_TABLE_NAME = 'users';)
        //$table->dropForeign('channels_user_id_foreign');
        Schema::dropIfExists('users');
    }
}
