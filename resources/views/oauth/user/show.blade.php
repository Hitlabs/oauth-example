@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{$user['firstname']}} {{$user['lastname']}}</div>
                    <div class="card-body">
                        <div class="list-group">
                             <span class="list-group-item">Firstname: {{$user['firstname']}}</span>
                             <span class="list-group-item">Lastname: {{$user['lastname']}}</span>
                             <span class="list-group-item">Email: {{$user['email']}}</span>
                             <span class="list-group-item">Username: {{$user['username']}}</span>
                             <span class="list-group-item">Pronto User ID: {{$user['id']}}</span>
                            <span class="list-group-item">Active: {{$user['active']}}</span>
                            <span class="list-group-item">Roles: {{$user['role']}}</span>
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="form-group row mb-0 offset-md-0">
                            <button onclick="window.location.href='/users/{{$user['id']}}/update'" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
