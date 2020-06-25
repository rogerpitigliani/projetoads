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
            $cl->default_invalidas = false;
            $cl->enabled = true;
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Invalidas Excedidas";
            $cl->descricao = "Invalidas Excedidas";
            $cl->default_timeout = false;
            $cl->default_invalidas = true;
            $cl->enabled = true;
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Solucionado";
            $cl->descricao = "Solucionado problema reportado pelo cliente";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Positiva";
            $cl->save();


            $cl = new Classificacao();
            $cl->classificacao = "Solucionado Parcial";
            $cl->descricao = "Solucionado Parcial";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Positiva";
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Problema Não Solucionado";
            $cl->descricao = "Problema Não Solucionado";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Negativa";
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Agendado Atendimento";
            $cl->descricao = "Agendado Atendimento";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Neutra";
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Envio Proposta";
            $cl->descricao = "Envio de Proposta";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Positiva";
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Pedido Realizado";
            $cl->descricao = "Pedido Realizado";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Positiva";
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Informação Produtos";
            $cl->descricao = "Informação Sobre Produtos";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Neutra";
            $cl->save();

            $cl = new Classificacao();
            $cl->classificacao = "Atendimento Abandonado";
            $cl->descricao = "Atendimento Abandonado";
            $cl->default_timeout = false;
            $cl->enabled = true;
            $cl->tipo = "Negativa";
            $cl->save();
        }
    }
}
