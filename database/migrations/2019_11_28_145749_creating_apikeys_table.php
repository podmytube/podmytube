<?php

use App\ApiKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingApikeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('apikey');
            $table->string('comment');
            $table->unsignedTinyInteger('environment')->default(ApiKey::_PROD_ENV);
            $table->boolean('active')->default(true);
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
        /* Schema::table('quotas', function (Blueprint $table) {
            $table->dropForeign('quotas_apikey_id_foreign');
        }); */
        Schema::dropIfExists('api_keys');
    }
}
