<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return response()->redirectTo('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix' => 'usuario', 'middleware' => ['auth']], function () {
    Route::resource('usuario', 'UsuarioController');
    Route::resource('equipe', 'EquipeController');
});

Route::group(['prefix' => 'configs', 'middleware' => ['auth']], function () {
    Route::resource('apiconfig', 'ApiConfigController');
    Route::resource('botconfig', 'BotConfigController');
    Route::resource('classificacao', 'ClassificacaoController');
});

Route::group(['prefix' => 'atendimento', 'middleware' => ['auth']], function () {
    Route::resource('atendimento', 'AtendimentoController');
    // Route::resource('botconfig', 'BotConfigController');
});

Route::group(['prefix' => 'relatorio', 'as' => 'relatorio.', 'middleware' => ['auth']], function () {
    Route::get('atendimentos', 'RelatorioController@atendimentos')->name('atendimentos.index');
    Route::post('atendimentos/data', 'RelatorioController@atendimentosData')->name('atendimentos.data');
});
