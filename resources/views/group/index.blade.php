@extends('app')

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-sm-12">
                <h1>
                    Groups
                </h1>
                @include('navigation.breadcrumb')
            </div>
        </div>
    </section>

    <section class="content">
        @include('errors.notifications')

        <div class="row">
            <div class="col-sm-12">
                <div style="margin-bottom: 10px" class="phonex-table-div clearfix">
                    <div class="left-cell">
                    </div>
                    <div class="right-cell">
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="text-right pull-right col-xs-2">Total: {{ $groups->total() }}</div>
                        </div>
                    </div>
                    <table class="table table-condensed phonex-table-sortable">
                        <tr>
                            <th>ID</th>
                            <th>Group name</th>
                            <th>Comment</th>
                            <th>Users count</th>
                            <th class="text-center">Options</th>
                        </tr>
                        @foreach($groups as $group)
                            <tr>
                                <td>{{ $group->id }}</td>
                                <td><a href="{{ \URL::route('users.index', [ 'user_group[]' => $group->id ]) }}">{{ $group->name or '' }}</a></td>
                                <td>{{ $group->comment }}</td>
                                <td>{{ $group->users()->count()  }}</td>
                                <td class="text-center">
                                    <div class="btn-group  btn-group-xs">
                                        {{--<a type="button" class="btn btn-info view-btn-edit" href="{{ \URL::route('users.edit', $user->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>--}}
                                        {{--<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="#" title="Delete user"><i class="fa fa-times-circle-o"></i></a>--}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="text-center">
                    {!! $groups->appends(Request::except('page'))->render(); !!}
                </div>
            </div>
        </div>

    </section>
@endsection
