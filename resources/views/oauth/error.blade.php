@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">OAuth Failed!</div>
                    <div class="card-body">
                        <div class="list-group">
                            <span class="list-group-item">{{$error}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
