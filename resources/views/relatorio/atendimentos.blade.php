@extends('layouts.app')

@section('content')
    <relatorio-atendimentos-component
        titulo="{{ $titulo }}"
        usuario="{{ json_encode($usuario) }}"
        usuarios="{{ json_encode($usuarios) }}"
        classificacoes="{{ json_encode($classificacoes) }}"
        url_data="{{ route('relatorio.atendimentos.data') }}"
        sort_by="id"
    ></relatorio-atendimentos-component>
@endsection
