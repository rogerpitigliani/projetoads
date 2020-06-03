<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMensagemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mensagem', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("atendimento_id");
            $table->string("direcao", 20)->default("in"); // in/out
            $table->string("type", 50)->default("text/plain");
            $table->string("content", 250)->nullable();
            $table->string("to", 150)->nullable();
            $table->bigInteger("usuario_id")->nullable();
            $table->timestamps();

            $table->foreign("atendimento_id", "fk_msg_atendimento")->references("id")->on("atendimento");
            $table->foreign("usuario_id", "fk_msg_usuario")->references("id")->on("usuario");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensagem');
    }
}
