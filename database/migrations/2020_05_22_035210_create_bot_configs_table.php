<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_config', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("msg_inicial", 250);
            $table->string("msg_menu", 200);
            $table->string("msg_invalid", 200);
            $table->string("msg_encaminhamento", 200);
            $table->string("msg_encerramento", 200);
            $table->string("msg_encerramento_tentativas", 200);
            $table->string("msg_encerramento_timeout", 200);
            $table->integer("timeout_encerra")->default(360);
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
        Schema::dropIfExists('bot_config');
    }
}
