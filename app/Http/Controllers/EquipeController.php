<?php

namespace App\Http\Controllers;

use App\Equipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EquipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->get('datatype') == 'json') {

            $regs = DB::table('equipe as e')->select('e.id', 'e.equipe', DB::raw('COUNT(u.id) as qtde_membros'))
                ->leftJoin('equipe_usuario as eu', 'eu.equipe_id', '=', 'e.id')
                ->leftJoin('usuario as u', 'eu.usuario_id', '=', 'u.id')
                ->groupBy("e.id", "e.equipe")
                ->orderBy("e.id", "asc")
                ->get();
            // return response()->json(Equipe::all());
            return response()->json($regs);
        } elseif ($request->get('datatype') == 'list') {
            $regs = Equipe::select('id as value', 'equipe as text')->orderBy('equipe', 'asc')->get();
            return response()->json($regs);
        } else {
            $titulo = "Equipes";
            return view("usuario/equipe", compact('titulo'));
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

            'equipe' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('equipe')
            ],

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 422);
        }

        $equipe = new Equipe();
        $equipe->equipe = $request->get('equipe');
        $equipe->usuario_id = Auth::user()->id;
        $equipe->save();

        $mrs = $request->get('membros_usuarios');
        $mids = [];
        foreach ($mrs as $mr) {
            $mids[] = $mr['value'];
        }
        $equipe->usuarios()->sync($mids);



        return response()->json([
            'status' => 'OK',
            'message' => 'Registro Criado',
            'editar' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function show(Equipe $equipe)
    {

        $e = Equipe::with('usuarios')->where('id', '=', $equipe->id)->first();
        return response()->json($e);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Equipe $equipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Equipe $equipe)
    {

        $rules = [

            'equipe' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('equipe')->ignore($equipe->id)
            ],

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 422);
        }

        $equipe->equipe = $request->get('equipe');
        $equipe->save();


        $mrs = $request->get('membros_usuarios');
        $mids = [];
        foreach ($mrs as $mr) {
            $mids[] = $mr['value'];
        }
        $equipe->usuarios()->sync($mids);

        return response()->json([
            'status' => 'OK',
            'message' => 'Registro Atualizado'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipe $equipe)
    {
        //
    }
}
