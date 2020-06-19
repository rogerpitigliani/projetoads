@extends('layouts.app')

@section('content')
    <classificacao-component
        titulo="{{ $titulo }}"
        url="{{ route('classificacao.index') }}"
        url_show="{{ route('classificacao.show','ID') }}"
        url_update="{{ route('classificacao.update','ID') }}"
        url_store="{{ route('classificacao.store') }}"
        sort_by="id"
    ></classificacao-component>
@endsection
