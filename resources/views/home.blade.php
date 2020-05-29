@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-primary ">
                <div class="card-header bg-primary text-white text-center">Sistema de Atendimento Chat Multicanal</div>

                <div class="card-body text-center" >
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <img src="{{ asset('img/bot_imagem.png') }}" class="img-fluid" >
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
