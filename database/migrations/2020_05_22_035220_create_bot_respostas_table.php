<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_resposta', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("resposta", 150); // suporte?
            $table->string("criterio", 30)->default("contem"); // contem|igual|comeca|termina
            $table->bigInteger("equipe_id");
            $table->timestamps();

            $table->foreign("equipe_id", "fk_botresp_equipe")->references("id")->on("equipe");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_resposta');
    }
}
