<?php

namespace App\Http\Controllers;

use App\Classificacao;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{

    public function atendimentos(Request $request)
    {

        $titulo = "Relatório Atendimentos";
        $usuario = Usuario::where('id', '=', Auth::user()->id)->first();

        $usuarios = Usuario::select('id as value', 'login as text')->orderBy('login', 'asc')->get();
        $classificacoes = Classificacao::select('id as value', 'classificacao as text')
            ->where('default_timeout', '=', false)
            ->where('default_invalidas', '=', false)
            ->orderBy('classificacao', 'asc')
            ->get();




        return view('relatorio/atendimentos', compact('titulo', 'usuario', 'usuarios', 'classificacoes'));
    }

    public function atendimentosData(Request $request)
    {
        return response()->json([]);
    }
}
