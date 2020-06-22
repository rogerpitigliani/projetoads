<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassificacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classificacao', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("classificacao", 100);
            $table->string("descricao", 200)->nullable();
            $table->string("tipo", 100)->default("Neutra"); // Positiva, Negativa ou Neutra
            $table->boolean("enabled")->default(true);
            $table->boolean("default_timeout")->default(false);
            $table->boolean("default_invalidas")->default(false);
            $table->index("tipo", "idx_classif_tipo");
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
        Schema::dropIfExists('classificacao');
    }
}
