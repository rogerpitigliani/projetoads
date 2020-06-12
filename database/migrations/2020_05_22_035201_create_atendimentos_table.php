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
            $table->string("status", 30); // new / bot / chat / end
            $table->string("protocolo", 50);
            $table->timestamp("datahora_inicio")->useCurrent();
            $table->timestamp("datahora_fila")->nullable();
            $table->timestamp("datahora_atende")->nullable();
            $table->timestamp("datahora_termino")->nullable();
            $table->bigInteger("equipe_id")->nullable();
            $table->bigInteger("usuario_id")->nullable();
            $table->bigInteger("contato_id")->nullable();
            $table->string("message_id", 200); // ID MENSAGENS
            $table->string("remote_id", 200); // ID REMOTO DO CLIENTE
            $table->bigInteger("classificacao_id")->nullable();
            $table->integer("invalidas")->default(0);
            $table->boolean("finalizado")->default(false);
            $table->timestamp("datahora_ultima_recebida")->useCurrent();
            $table->timestamps();

            $table->index("protocolo", "idx_atendimento_protocolo");
            $table->index("message_id", "idx_atendimento_messageid");

            $table->foreign("classificacao_id", "fk_atendimento_classificacao")->references("id")->on("classificacao");
            $table->foreign("equipe_id", "fk_atendimento_equipe_id")->references("id")->on("equipe");
            $table->foreign("contato_id", "fk_atendimento_contatoid")->references("id")->on("contato");
            $table->foreign("usuario_id", "fk_atendimento_usuario_id")->references("id")->on("usuario");
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
