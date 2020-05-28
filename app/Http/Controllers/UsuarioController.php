<?php

namespace App\Http\Controllers;

use App\User;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->get('datatype') == 'json') {
            return response()->json(User::all());
        } elseif ($request->get('datatype') == 'list') {
            $regs = User::select('id as value', 'login as text')->orderBy('login', 'asc')->get();
            return response()->json($regs);
        } else {
            $titulo = "Usuários";
            return view("usuario/usuario", compact('titulo'));
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
            'password' => [
                'required',
                'string',
                'min:6',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'password_confirmation' => 'required_with:password|same:password|min:6',
            'login' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('usuario')
            ],
            'name' => [
                'required',
                'string',
                'min:4',
                'max:100',
            ],
            'email' => 'email'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 422);
        }

        $user = new User();
        $user->name = $request->get('name');
        $user->login = $request->get('login');
        $user->password = Hash::make(trim($request->get('password')));
        $user->admin = $request->get('admin');
        $user->supervisor = $request->get('supervisor');
        $user->atendente = $request->get('atendente');
        $user->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Registro Criado'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show(Usuario $usuario)
    {
        return response()->json($usuario);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usuario $usuario)
    {
        $rules = [

            'login' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('usuario')->ignore($usuario->id)
            ],
            'name' => [
                'required',
                'string',
                'min:4',
                'max:100',
            ],
            'email' => 'email'
        ];

        if ($request->get('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:6',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ];
            $rules['password_confirmation'] = 'required_with:password|same:password|min:6';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 422);
        }

        $usuario->name = $request->get('name');
        $usuario->login = $request->get('login');
        $usuario->password = Hash::make(trim($request->get('password')));
        $usuario->admin = $request->get('admin');
        $usuario->supervisor = $request->get('supervisor');
        $usuario->atendente = $request->get('atendente');
        $usuario->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Registro Atualizado'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usuario $usuario)
    {
        //
    }
}
