@extends('layouts.chat')

@section('content')

    <atendimento-component
        socket_host="{{ env('SOCKET_HOST') }}"
        socket_port="{{ env('SOCKET_PORT') }}"
        usuario_id="{{ Auth::user()->id }}"
        titulo="{{$titulo}}"
        usuario="{{ json_encode($usuario) }}"
    ></atendimento-component>

@endsection
