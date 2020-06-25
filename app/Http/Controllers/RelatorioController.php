<?php

namespace App\Http\Controllers;

use App\Atendimento;
use App\Classificacao;
use App\Contato;
use App\Equipe;
use App\Mensagem;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{

    public function atendimentos(Request $request)
    {

        $titulo = "Relatório Atendimentos";
        $usuario = Usuario::where('id', '=', Auth::user()->id)->first();
        $equipes = Equipe::select('id as value', 'equipe as text')->orderBy('equipe', 'asc')->get();
        $usuarios = Usuario::select('id as value', 'login as text')->orderBy('login', 'asc')->get();
        $classificacoes = Classificacao::select('id as value', 'classificacao as text')
            ->where('default_timeout', '=', false)
            ->where('default_invalidas', '=', false)
            ->orderBy('classificacao', 'asc')
            ->get();

        // return response()->json($equipes);

        return view('relatorio/atendimentos', compact('titulo', 'usuario', 'usuarios', 'classificacoes', 'equipes'));
    }

    public function atendimentoMensagens(Request $request, $id)
    {

        $a = DB::table("atendimento as a")
            ->select(
                "a.*",
                DB::raw("COALESCE(c.full_name,'desconhecido') as full_name"),
                DB::raw("COALESCE(u.login,'desconhecido') as login"),
                DB::raw("COALESCE(cl.classificacao,'Não Classificado') as classificacao")
            )
            ->leftJoin('usuario as u', 'a.usuario_id', '=', 'u.id')
            ->leftJoin('contato as c', 'a.contato_id', '=', 'c.id')
            ->leftJoin('classificacao as cl', 'a.classificacao_id', '=', 'cl.id')
            ->where("a.id", "=", $id)
            ->first();

        $c = null;
        if ($a->contato_id) {
            $c = Contato::find($a->contato_id);
        }


        $msgs = DB::table('mensagem as m')
            ->select(
                'm.*',
                DB::raw("COALESCE(c.photo_uri,'/img/bot_imagem.png') as photo_uri"),
                DB::raw("COALESCE(c.full_name,'desconhecido') as full_name")
            )
            ->leftJoin('atendimento as a', 'm.atendimento_id', '=', 'a.id')
            ->leftJoin('contato as c', 'a.contato_id', '=', 'c.id')
            ->where('atendimento_id', '=', $id)
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json([
            "messages" => $msgs,
            "atendimento" => $a,
            "contato" => $c
        ]);
    }

    public function atendimentosData(Request $request)
    {

        $ini = $request->get("periodo")["startDate"];
        $end = $request->get("periodo")["endDate"];

        $equipe_id = $request->get("equipe_id");
        $usuario_id = $request->get("usuario_id");
        $classificacao_id = $request->get("classificacao_id");
        $canal = $request->get("canal");
        $text = $request->get("text");

        $regs = DB::table("atendimento as a")
            ->select(
                "a.*",
                DB::raw("COALESCE(u.login,'-') as login"),
                DB::raw("COALESCE(e.equipe,'-') as equipe"),
                DB::raw("COALESCE(c.classificacao,'-') as classificacao")
            )
            ->leftJoin('usuario as u', 'u.id', '=', 'a.usuario_id')
            ->leftJoin('equipe as e', 'e.id', '=', 'a.equipe_id')
            ->leftJoin('classificacao as c', 'c.id', '=', 'a.classificacao_id')
            ->whereBetween('datahora_inicio', [$ini, $end])
            ->orderBy("a.id", "desc");


        if ($equipe_id) {
            $regs = $regs->where("a.equipe_id", "=", $equipe_id);
        }
        if ($classificacao_id) {
            $regs = $regs->where("a.classificacao_id", "=", $classificacao_id);
        }
        if ($usuario_id) {
            $regs = $regs->where("a.usuario_id", "=", $usuario_id);
        }
        if ($canal) {
            $regs = $regs->where("a.canal", "=", $canal);
        }
        if ($text) {
            $regs = $regs->whereExists(function ($query) use ($text) {
                $query->select(DB::raw(1))
                    ->from("mensagem")
                    ->whereRaw('mensagem.atendimento_id = a.id')
                    ->where("mensagem.content", "ILIKE", "%" . $text . "%");
            })->orWhere("a.protocolo", "=", $text);
        }


        // dd($regs->toSql());

        $regs = $regs->get();


        return response()->json($regs);
    }
}
