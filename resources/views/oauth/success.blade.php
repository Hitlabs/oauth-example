@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">OAuth Succesful!</div>
                    <div class="card-body">
                        <div class="list-group">
                            <span class="list-group-item">Access Token: {{$access_token}}</span>
                            <span class="list-group-item">Refresh Token: {{$refresh_token}}</span>
                            <span class="list-group-item">Expiry: {{$expiry}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
