<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        // dd(request()->segment(1));

        if (Auth::user()->admin ||  Auth::user()->supervisor) {
            $titulo = "Dashboard";
            $usuario = Usuario::where('id', '=', Auth::user()->id)->first();
            return view('dashboard', compact('titulo', 'usuario'));
        } else {
            return view('home');
        }
    }
}
