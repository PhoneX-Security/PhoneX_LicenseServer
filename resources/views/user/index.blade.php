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
                    <div class="col-sm-9">

                        <form class="form-inline inline-block" action="{{ route('users.index') }}" method="get">

                            <div class="input-group">
                                <input type="text" name="username" value="{{Request::get('username')}}" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search by username">
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>

                        {{--<form class="form-inline inline-block" action="{{ route('users.index') }}" method="get">--}}

                            {{--<div class="input-group">--}}
                                {{--<input type="text" name="comment" value="{{Request::get('comment')}}" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search by comment">--}}
                                {{--<div class="input-group-btn">--}}
                                    {{--<button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</form>--}}

                        <form class="form-inline inline-block "  action="{{ \URL::route('users.index') }}" method="get">
                            @if(\Request::has('o')) <input type="hidden" name="o" value="{{ request('o') }}" /> @endif
                            @if(\Request::has('s')) <input type="hidden" name="s" value="{{ request('s') }}" /> @endif

                            <div class="form-group side-margin-small">
                                <label for="input-limit">Limit:</label>
                                <input type="number" style="width: 70px" class="form-control" id="input-limit" name="limit" value="{{ request('limit', 20)}}">
                            </div>

                            <div class="form-group">
                                <label for="last-activity-from" class="inline-block">Last activity (from):</label>
                            </div>

                            <div class="input-group side-margin-small date">
                                <input value="{{ request('last_activity_from') }}" id="last-activity-from" type="text" name="last_activity_from" class="form-control" />
                                <span class="input-group-addon" ><i class="glyphicon glyphicon-th"></i></span>
                            </div>

                            <div class="form-group side-margin-small">
                                <label class="checkbox-inline" for="checkboxes-0">
                                    {!! Form::checkbox('with_licenses', '1', \Request::has('with_licenses'), ['id'=>'checkboxes-0']) !!}
                                    With licenses only
                                </label>
                            </div>

                            {{--<div class="form-group side-margin-small">--}}
                                {{--<label class="checkbox-inline" for="checkboxes-1">--}}
                                    {{--{!! Form::checkbox('with_', '1', \Request::has('paying_only'), ['id'=>'checkboxes-1']) !!}--}}
                                    {{--With purchased (Full/Premium/Basic) only--}}
                                {{--</label>--}}
                            {{--</div>--}}

                            <div class="form-group">
                                <label for="product_input">License:</label>
                                <select id="product_input" name="products[]" class="multiselect-basic"  multiple="multiple">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" @if($product->selected) selected="selected" @endif>{{ $product->xname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{--<div class="form-group">--}}
                                {{--<label for="group_input">Group:</label>--}}
                                {{--<select id="group_input" name="user_group[]" class="multiselect-basic"  multiple="multiple">--}}
                                    {{--@foreach($groups as $group)--}}
                                        {{--<option value="{{ $group->id }}" @if($group->selected) selected="selected" @endif>{{ $group->name }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            <button type="submit" class="btn btn-default side-margin-small">Submit</button>
                        </form>

                    </div>
                    <div class="col-sm-3 text-right">

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
                                {{--<th>{!! link_to_sort('email', 'E-mail') !!}</th>--}}
                                <th>{!! link_to_sort('date_last_activity','Last activity') !!}</th>
                                <th>{!! link_to_sort('issued_on', 'Current license from') !!}</th>
                                <th>{!! link_to_sort('expires_on', 'Current license to') !!}</th>
                                <th>Purchased licenses</th>
                                <th>Phone / Version</th>
                                <th>Location</th>
                                <th >Groups</th> {{-- width="15%" --}}
                                {{--<th>Roles</th>--}}
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
                                    {{--<td>{{ $user->email or '' }}</td>--}}

                                    <td>@if($user->subscriber) {{ $user->subscriber->date_last_activity }} @endif</td>
{{--                                    <td>@if($user->subscriber) {{ $user->subscriber->expires_on }} @endif</td>--}}
                                    <td>@if($user->subscriber) {{ $user->subscriber->issued_on }} @endif</td>
                                    <td>@if($user->subscriber) {{ $user->subscriber->expires_on }} @endif</td>
                                    {{--<td>{{ $user->activeLicense->expires_at or '' }}</td>--}}
                                    <td>
                                        @foreach($user->licenseProducts as $k=>$licProd)
                                            @if($k>0), @endif
                                            <?php $expired = $licProd->expires_at < \Carbon\Carbon::now(); ?>
                                            @if($expired)<strike style="color: grey">@endif
                                            @if($licProd->product->display_name)
                                                    {{$licProd->product->display_name}}
                                            @else
                                                    {{ucfirst($licProd->product->name)}}
                                            @endif
                                            @if($expired)</strike>@endif
                                        @endforeach
                                    </td>
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
                                        @foreach($user->groups as $k => $group)
                                            @if($k > 0), @endif
                                            {{ $group->name }}
                                        @endforeach
                                    </td>

                                    {{--ROLES NOT NECCESSARY TO BE SHOWN --}}
                                    {{--<td>--}}
                                        {{--{{ $user->roles_list }}--}}
                                    {{--</td>--}}

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
                        Total {{ $users->total() }} entries</div>
                </div>


                <div class="pull-right">
                    {!! $users->appends(Request::except('page'))->render() !!}
                </div>

            </div>
        </div>
    </section>
@endsection