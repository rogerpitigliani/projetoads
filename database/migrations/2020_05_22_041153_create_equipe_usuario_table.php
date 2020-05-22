<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipeUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipe_usuario', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("equipe_id");
            $table->bigInteger("usuario_id");
            $table->timestamps();
            $table->foreign("usuario_id", "fk_equiusu_usuarioid")->references("id")->on("usuario");
            $table->foreign("equipe_id", "fk_equiusu_equipeid")->references("id")->on("equipe");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipe_usuario');
    }
}
