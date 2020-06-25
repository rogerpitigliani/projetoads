@extends('layouts.app')

@section('content')
    <dashboard-admin-component
        titulo="{{ $titulo }}"
        usuario="{{ json_encode($usuario) }}"
        url_data="{{ route ('dashboard.data') }}"
        sort_by="id"
    ></dashboard-admin-component>
@endsection
