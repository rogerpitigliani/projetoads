<?php

namespace App\Http\Controllers;

use App\BotConfig;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        //
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
