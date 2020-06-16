@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Groups</div>
                    <div class="card-body">
                        <div class="list-group">
                            <span class="list-group-item">Total Records: {{$total}}, Records Displayed: {{$pagesize}}, Start: {{$start}}</span>
                            <span class="list-group-item">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Group ID</th>
                                            <th scope="col">Group Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($groups) === 0)
                                        <tr>
                                            <td>No Groups Found.</td>
                                        </tr>
                                    @else
                                        @foreach ($groups as $group)
                                        <tr>
                                            <td>{{$group['id']}}</td>
                                            <td>{{$group['group_name']}}</td>
                                            <td>{{implode(', ', Arr::pluck($group['members'], 'display'))}}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </span>
                            <span class="list-group-item">
                                @if ($start > 1)
                                    <span class="float-left"><a href="/groups/list?start={{max(1, $start - $pagesize)}}">Previous Page</a></span>
                                @endif
                                @if ($total > ($pagesize + $start))
                                    <span class="float-right"><a href="/groups/list?start={{$pagesize + $start}}">Next Page</a></span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
