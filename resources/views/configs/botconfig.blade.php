@extends('layouts.app')

@section('content')
<div class="container">
    <botconfig-component
        titulo="{{ $titulo }}"
        url_update="{{ route('botconfig.update','ID') }}"
        url_store="{{ route('botconfig.store') }}"
        sort_by="id"
        usuario="{{ json_encode($usuario) }}"
        botconfig="{{ json_encode($botconfig) }}"
    ></botconfig-component>
</div>
@endsection
