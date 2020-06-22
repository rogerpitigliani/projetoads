<?php

namespace App\Http\Controllers;

use App\Classificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ClassificacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->get('datatype') == 'json') {
            return response()->json(Classificacao::all());
        } elseif ($request->get('datatype') == 'list') {

            $regs = Classificacao::select('id as value', 'classificacao as text')
                ->where('enabled', '=', false)
                ->orderBy('classificacao', 'asc')
                ->get();

            return response()->json($regs);
        } else {
            $titulo = "Classificação";
            return view("configs/classificacao", compact('titulo'));
        }
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
        $rules = [
            'classificacao' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('classificacao')
            ],
            'tipo' => [
                'required',
                'string',
                'min:4',
                'max:100',
                Rule::in(['Neutra', 'Positiva', 'Negativa']),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 422);
        }

        $cl = new Classificacao();
        $cl->classificacao = $request->get('classificacao');
        $cl->descricao = $request->get('descricao');
        $cl->tipo = $request->get('tipo');
        $cl->enabled = $request->get('enabled');
        $cl->default_timeout = ($request->get('default_timeout') == true);
        $cl->default_invalidas = ($request->get('default_invalidas') == true);
        $cl->save();

        if ($cl->default_timeout) {
            Classificacao::where('id', '<>', $cl->id)
                ->update([
                    'default_timeout' => false
                ]);
        }

        if ($cl->default_invalidas) {
            Classificacao::where('id', '<>', $cl->id)
                ->update([
                    'default_invalidas' => false
                ]);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Registro Criado'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Classificacao  $classificacao
     * @return \Illuminate\Http\Response
     */
    public function show(Classificacao $classificacao)
    {
        return response()->json($classificacao);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Classificacao  $classificacao
     * @return \Illuminate\Http\Response
     */
    public function edit(Classificacao $classificacao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Classificacao  $classificacao
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classificacao $classificacao)
    {
        $rules = [
            'classificacao' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('classificacao')->ignore($classificacao->id)
            ],
            'tipo' => [
                'required',
                'string',
                'min:4',
                'max:100',
                Rule::in(['Neutra', 'Positiva', 'Negativa']),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 422);
        }

        $classificacao->classificacao = $request->get('classificacao');
        $classificacao->descricao = $request->get('descricao');
        $classificacao->tipo = $request->get('tipo');
        $classificacao->enabled = ($request->get('enabled') == true);
        $classificacao->default_timeout = ($request->get('default_timeout') == true);
        $classificacao->default_invalidas = ($request->get('default_invalidas') == true);
        $classificacao->save();

        if ($classificacao->default_timeout) {
            Classificacao::where('id', '<>', $classificacao->id)
                ->update([
                    'default_timeout' => false
                ]);
        }

        if ($classificacao->default_invalidas) {
            Classificacao::where('id', '<>', $classificacao->id)
                ->update([
                    'default_invalidas' => false
                ]);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Registro Atualizado'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Classificacao  $classificacao
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classificacao $classificacao)
    {
        //
    }
}
