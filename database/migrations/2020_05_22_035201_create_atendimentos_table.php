<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtendimentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atendimento', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("canal", 30); // facebook/site/whatsapp/telegram
            $table->string("estagio", 30); // new / bot / chat / end
            $table->string("remoteid", 100);
            $table->timestamp("datahora_inicio");
            $table->timestamp("datahora_termino");
            $table->timestamp("datahora_fila");
            $table->timestamp("datahora_atende");
            $table->bigInteger("equipe_id")->nullable();
            $table->bigInteger("usuario_id")->nullable();
            $table->string("remote_id", 200); // ID REMOTO DO CLIENTE
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
        Schema::dropIfExists('atendimento');
    }
}
