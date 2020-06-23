@extends('layouts.app')

@section('content')
    <relatorio-atendimentos-component
        titulo="{{ $titulo }}"
        usuario="{{ json_encode($usuario) }}"
        usuarios="{{ json_encode($usuarios) }}"
        classificacoes="{{ json_encode($classificacoes) }}"
        equipes="{{ json_encode($equipes) }}"
        url_data="{{ route('relatorio.atendimentos.data') }}"
        url_mensagens="{{ route('relatorio.atendimento.mensagens',':ID') }}"
        sort_by="id"
    ></relatorio-atendimentos-component>
@endsection
