@extends('layouts.app')

@section('content')
<div class="container">
    <equipe-component
    titulo="{{ $titulo }}"
    url="{{ route('equipe.index') }}"
    url_show="{{ route('equipe.show','ID') }}"
    url_update="{{ route('equipe.update','ID') }}"
    url_store="{{ route('equipe.store') }}"
    sort_by="id"
    url_usuarios="{{ route('usuario.index') }}"
></equipe-component>
</div>
@endsection
