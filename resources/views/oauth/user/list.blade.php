@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        <div class="list-group">
                            <span class="list-group-item">Total Records: {{$total}}, Records Displayed: {{$pagesize}}, Start: {{$start}}</span>
                            <span class="list-group-item">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">First Name</th>
                                            <th scope="col">Last Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Roles</th>
                                            <th scope="col">Pronto User ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     @if (count($users) === 0)
                                         <tr>
                                            <td>No Users Found.</td>
                                         </tr>
                                     @else
                                         @foreach ($users as $user)
                                             <tr>
                                                 <td>{{$user['firstname']}}</td>
                                                 <td>{{$user['lastname']}}</td>
                                                 <td>{{$user['email']}}</td>
                                                 <td>{{$user['roles']}}</td>
                                                 <td><a href="/users/{{$user['id']}}/show">{{$user['id']}}</a></td>
                                             </tr>
                                         @endforeach
                                     @endif

                                    </tbody>
                                </table>
                            </span>
                            <span class="list-group-item">
                                @if ($start > 1)
                                    <span class="float-left"><a href="/users/list?start={{max(1, $start - $pagesize)}}">Previous Page</a></span>
                                @endif
                                @if ($total > ($pagesize + $start))
                                    <span class="float-right"><a href="/users/list?start={{$pagesize + $start}}">Next Page</a></span>
                                @endif
                            </span>

                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
