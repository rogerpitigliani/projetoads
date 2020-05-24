@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card border-primary ">
                <div class="card-header bg-primary text-white text-center">{{ $titulo }}</div>
                <div class="card-body">

                    <div class="row">
                        <div class="col">
                            <div class="float-right">
                                {!! $users->links() !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                        <table class="table table-sm" id="tbl">
                            <thead>
                              <tr>
                                 <th>ID</th>
                                 <th>Nome</th>
                                 <th>Login</th>
                                 <th>Email</th>
                                 <th>&nbsp;</th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach($users as $user)
                              <tr>
                                 <td>{{ $user->id }}</td>
                                 <td>{{ $user->name }}</td>
                                 <td>{{ $user->login }}</td>
                                 <td>{{ $user->email }}</td>
                                 <td><button class="btn btn-sm btn-light float-right" title="Editar"><i class="far fa-edit"></i></button></td>
                              </tr>
                              @endforeach
                           </tbody>
                        </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
