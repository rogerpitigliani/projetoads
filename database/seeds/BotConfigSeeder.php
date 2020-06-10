<?php

use App\BotConfig;
use App\BotResposta;
use App\Equipe;
use Illuminate\Database\Seeder;

class BotConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $bc = BotConfig::orderBy("id", "desc")->first();

        if (!$bc) {
            $bc = new BotConfig();
            $bc->msg_inicial = "ProjetoADS.\nEste é um sistema de demonstração!";
            $bc->msg_menu  = "Por favor selecione o assunto desejado: \n1 - Suporte \n2 - Comercial";
            $bc->msg_invalid = "Ops! Opção invalida, tente novamente!";
            $bc->msg_encaminhamento = "OK, entendi! Encaminhando para equipe de atendimento.";
            $bc->msg_encerramento = "Obrigado pelo contato, seu protocolo de atendimento é :PROTOCOLO.\nAté mais.";
            $bc->msg_encerramento_tentativas = "Ops!, Tentativas excedidas... :( \nEncerrando Atendimento..\nTente novamente mais tarde...";
            $bc->msg_encerramento_timeout = "Infelizmente você demorou para responder.Vou encerrar esse atendimento, mas qualquer coisa me chame novamente.\nAté mais!";
            $bc->timeout_encerra = 45;
            $bc->save();

            $eq = Equipe::where('equipe', '=', 'Suporte')->first();
            $br = new BotResposta();
            $br->resposta = "1";
            $br->criterio = "=";
            $br->equipe_id = $eq->id;
            $br->save();
            $br = new BotResposta();
            $br->resposta = "suporte";
            $br->criterio = "=";
            $br->equipe_id = $eq->id;
            $br->save();

            $eq = Equipe::where('equipe', '=', 'Comercial')->first();
            $br = new BotResposta();
            $br->resposta = "2";
            $br->criterio = "=";
            $br->equipe_id = $eq->id;
            $br->save();
            $br = new BotResposta();
            $br->resposta = "comercial";
            $br->criterio = "=";
            $br->equipe_id = $eq->id;
            $br->save();
        }
    }
}
