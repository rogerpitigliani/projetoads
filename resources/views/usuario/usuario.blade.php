@extends('layouts.app')

@section('content')
    <usuario-component
        titulo="{{ $titulo }}"
        url="{{ route('usuario.index') }}"
        url_show="{{ route('usuario.show','ID') }}"
        url_update="{{ route('usuario.update','ID') }}"
        url_store="{{ route('usuario.store') }}"
        sort_by="id"
    ></usuario-component>
@endsection
