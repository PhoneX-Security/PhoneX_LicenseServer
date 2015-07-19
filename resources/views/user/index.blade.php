@extends('content-with-header')

@section('title', 'Users')
@section('subtitle', 'Manage')

@section('content')
    @parent

    <section class="content">

        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Users</h3>
            </div><!-- /.box-header -->
            <div class="box-body">

                <div class="row form-inline" style="margin-bottom: 5px">
                    <div class="col-sm-6">

                        {{--<form class="form-horizontal" action="{{ route('users.index') }}" method="get">--}}

                            {{--<div class="input-group">--}}
                                {{--<input type="search" class="form-control input-sm" name="username"--}}
                                       {{--value="{{ Input::get('username') }}" placeholder="Search">--}}
                                {{--<span class="input-group-btn">--}}
                                    {{--<button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>--}}
                                {{--</span>--}}
                            {{--</div>--}}
                        {{--</form>--}}

                        <form class="form-inline inline-block" action="{{ route('users.index') }}" method="get">

                            <div class="input-group">
                                <input type="text" name="username" value="{{Request::get('username')}}" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search by username">
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>

                        <form class="form-inline inline-block"  action="{{ \URL::route('users.index') }}" method="get">
                            <div class="form-group">
                                <label for="group_input">Group</label>
                                <select id="group_input" name="user_group[]" class="multiselect-basic"  multiple="multiple">
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" @if($group->selected) selected="selected" @endif>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-default">Filter</button>
                        </form>


                        {{--<div class="left-cell">--}}
                        {{--<form class="form-horizontal" style="width: 25%" action="{{ \URL::route('users.index') }}" method="get">--}}

                        {{--<div class="input-group">--}}
                        {{--<input type="search" class="form-control input-sm" name="username" value="{{ Input::get('username') }}" placeholder="Searchsssss">--}}
                        {{--<span class="input-group-btn">--}}
                        {{--<button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>--}}
                        {{--</span>--}}
                        {{--</div>--}}
                        {{--</form>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<label>Show <select--}}
                                        {{--name="example1_length" aria-controls="example1"--}}
                                        {{--class="form-control input-sm">--}}
                                    {{--<option value="10">10</option>--}}
                                    {{--<option value="25">25</option>--}}
                                    {{--<option value="50">50</option>--}}
                                    {{--<option value="100">100</option>--}}
                                {{--</select> entries</label>--}}
                        {{--</div>--}}
                    </div>
                    <div class="col-sm-6 text-right">

                        <a class="btn btn-sm btn-primary view-btn-create" href="/users/create">
                            <i class="fa fa-plus-circle"></i> New User
                        </a>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <table id="example1" class="table table-bordered table-striped">

                            <thead>
                            <tr>
                                <th>{!! link_to_sort('id', 'ID') !!}</th>
                                <th>{!! link_to_sort('username', 'Username') !!}</th>
                                <th>{!! link_to_sort('email', 'E-mail') !!}</th>
                                <th width="15%">Groups</th>
                                <th>Last activity</th>
                                <th>Active lic. expiration</th>
                                <th>Phone / Version</th>
                                <th>Location</th>
                                <th>Roles</th>
                                <th class="text-center">Options</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                                <tr>

                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <a href="{{ \URL::route('users.show', [ $user->id ]) }}">{{ $user->username }}</a>
                                    </td>
                                    <td>{{ $user->email or '' }}</td>
                                    <td>
                                        @foreach($user->groups as $k => $group)
                                            @if($k > 0), @endif
                                            {{ $group->name }}
                                        @endforeach
                                    </td>

                                    <td>@if($user->subscriber) {{ $user->subscriber->date_last_activity }} @endif</td>
{{--                                    <td>@if($user->subscriber) {{ $user->subscriber->expires_on }} @endif</td>--}}
                                    <td>{{ $user->activeLicense->expires_at or '' }}</td>
                                    <td>
                                        @if($user->subscriber && $user->subscriber->app_version)
                                            {{$user->subscriber->app_version_obj->platformDesc() . " / " . $user->subscriber->app_version_obj->versionDesc()}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->subscriber && $user->subscriber->location)
                                            {{$user->subscriber->location['country']}}
                                            @if($user->subscriber->location['city'])
                                                {{ ", " . $user->subscriber->location['city'] }}
                                            @endif
                                        @endif
                                    </td>

                                    <td>
                                        {{ $user->roles_list }}
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group  btn-group-xs">
                                            <a class="btn btn-info view-btn-edit" href="{{ route('users.show', $user->id) }}" title="Details"><i class="fa fa-pencil-square-o"></i> Details</a>
                                            <a class="btn btn-info view-btn-edit" href="{{ route('users.licenses', $user->id) }}" title="Licenses"><i class="fa fa-book"></i> Licenses</a>
                                            <a class="btn btn-info view-btn-edit" href="{{ route('users.cl', $user->id) }}" title="Contact List"><i class="fa fa-list-alt"></i> Contact List</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.box-body -->

            <div class="box-footer clearfix">
                <div class="pull-left">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                        {{--Showing 1 to 10 of --}}
                        Total {{ $users->total() }} entries</div>
                </div>


                <div class="pull-right">
                    {!! $users->appends(Request::except('page'))->render(); !!}
                </div>

            </div>
        </div><!-- /.box -->
    </section>
@endsection