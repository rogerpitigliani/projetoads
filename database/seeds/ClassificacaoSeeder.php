<?php

use App\Classificacao;
use Illuminate\Database\Seeder;

class ClassificacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $cl = Classificacao::first();
        if (!$cl) {
            $cl = new Classificacao();
            $cl->classificacao = "Timeout Resposta";
            $cl->descricao = "Timeout Resposta Bot";
            $cl->default_timeout = true;
            $cl->enabled = true;
            $cl->save();
        }
    }
}
