<?php

namespace App\Http\Controllers;

use App\BotConfig;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BotConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // if (!Auth::user()->admin  && !Auth::user()->supervisor) {
        //     return response()->redirectToRoute('home');
        // }

        $titulo = "Configurações de Atendimento BOT";
        $usuario = Usuario::find(Auth::user()->id);
        $botconfig = BotConfig::orderBy("id", "desc")->first();
        return view('configs/botconfig', compact('titulo', 'usuario', 'botconfig'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BotConfig  $botConfig
     * @return \Illuminate\Http\Response
     */
    public function show(BotConfig $botConfig)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BotConfig  $botConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(BotConfig $botConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BotConfig  $botConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BotConfig $botConfig)
    {
        $rules = [
            'msg_encaminhamento' => ['required', 'string', 'min:4', 'max:200'],
            'msg_encerramento' => ['required', 'string', 'min:4', 'max:200'],
            'msg_encerramento_tentativas' => ['required', 'string', 'min:4', 'max:200'],
            'msg_encerramento_timeout' => ['required', 'string', 'min:4', 'max:200'],
            'msg_inicial' => ['required', 'string', 'min:4', 'max:200'],
            'msg_invalid' => ['required', 'string', 'min:4', 'max:200'],
            'msg_menu' => ['required', 'string', 'min:4', 'max:200'],
            'timeout_encerra' => ['required', 'numeric', 'min:10', 'max:3600'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 422);
        }

        $bc = new BotConfig();
        $bc->msg_encaminhamento = $request->get('msg_encaminhamento');
        $bc->msg_encerramento = $request->get('msg_encerramento');
        $bc->msg_encerramento_tentativas = $request->get('msg_encerramento_tentativas');
        $bc->msg_encerramento_timeout = $request->get('msg_encerramento_timeout');
        $bc->msg_inicial = $request->get('msg_inicial');
        $bc->msg_invalid = $request->get('msg_invalid');
        $bc->msg_menu = $request->get('msg_menu');
        $bc->timeout_encerra = $request->get('timeout_encerra');
        $bc->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Alterações Aplicadas'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BotConfig  $botConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(BotConfig $botConfig)
    {
        //
    }
}
