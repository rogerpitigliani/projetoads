<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContatosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contato', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("full_name", 200)->nullable();
            $table->string("identity", 200)->nullable();
            $table->string("photo_uri", 200)->nullable();
            $table->string("source", 100)->nullable();
            $table->timestamps();

            $table->index('identity', 'idx_contato_identity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contato');
    }
}
