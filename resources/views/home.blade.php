@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Your User Details</div>
                    <div class="card-body">
                        <div class="list-group">
                            <span class="list-group-item">Name: {{$user->name}}</span>
                            <span class="list-group-item">Email: {{$user->email}}</span>
                            @if ($configured)
                                @if ($oauth_connected)
                                    @if ($oauth_expired)
                                        <span class="list-group-item text-danger">OAuth Expired!</span>
                                    @else
                                        <span class="list-group-item">OAuth Connected!</span>
                                        <span class="list-group-item"><a href="/groups/list">List groups with SCIM API</a></span>
                                        <span class="list-group-item"><a href="/users/list">List users with SCIM API</a></span>
                                        <span class="list-group-item"><a href="/users/create">Create a user with SCIM API</a></span>
                                    @endif
                                    <span class="list-group-item"><a href="/oauth/refresh">Refresh OAuth Token</a> | <a href="{{$url}}">OAuth Reconnect</a></span>
                                @else
                                    <span class="list-group-item"><a href="{{$url}}">Connect OAuth</a></span>
                                @endif
                            @else
                                <span class="list-group-item text-danger">
                                    <span class="float-left">docker-compose.yml is not configured properly</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
