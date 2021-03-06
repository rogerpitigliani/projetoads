<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name", 100);
            $table->string("login", 30);
            $table->string("password", 200);

            $table->boolean("admin")->default(false);
            $table->boolean("supervisor")->default(false);
            $table->boolean("atendente")->default(true);

            $table->boolean("logado")->default(false);
            $table->boolean("pausa")->default(false);
            $table->boolean("chat")->default(false);

            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('usuario');
    }
}
