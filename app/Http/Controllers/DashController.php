<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashController extends Controller
{
    public function index()
    {

        // dd(request()->segment(1));

        return view('dashboard');
    }
}
