@extends('app')

@section('content')

<div class="container">
    <section class="content-header">
        <div class="row">
            <div class="col-sm-12">
                <h1>
                    Licenses
                    <small>Manage</small>
                </h1>
                @include('navigation.breadcrumb')
            </div>
        </div>
    </section>

    <section class="content">

        @include('errors.notifications')

        <div class="row">
            <div class="col-sm-12">
                <div style="margin-bottom: 10px">
                    <form class="form-inline">
                        {{--<a class="btn btn-sm btn-primary view-btn-create" href="/users/create">--}}
                            {{--<i class="fa fa-plus-circle"></i> New User--}}
                        {{--</a>--}}

                        <form class="form-horizontal" style="width: 10%" action="{{ \URL::route('licenses.index') }}" method="get">

                            <div class="input-group">
                                <input type="search" class="form-control input-sm" name="username" value="{{ Input::get('username') }}" placeholder="Search">
                        <span class="input-group-btn">
                            <button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                        </span>
                            </div>
                        </form>
                    </form>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">

                            <div class="col-xs-8 pull-left">
                                <form method="get" class="form-inline">
                                    @if(\Request::has('o')) <input type="hidden" name="o" value="{{ InputGet::get('o') }}" /> @endif
                                    @if(\Request::has('s')) <input type="hidden" name="s" value="{{ InputGet::get('s') }}" /> @endif
                                    <div class="form-group">
                                        <label class="col-md-1 control-label text-le">Filters:</label>
                                    </div>
                                    <div class="form-group">
                                        <label class="checkbox-inline" for="checkboxes-0">
                                            {!! Form::checkbox('active_only', '1', \Request::has('active_only'), ['id'=>'checkboxes-0']); !!}
                                            Active only
                                        </label>

                                        <label class="checkbox-inline" for="checkboxes-1">
                                            {!! Form::checkbox('trial_only', '1', \Request::has('trial_only'), ['id'=>'checkboxes-1']); !!}
                                            Trial only
                                        </label>

                                    </div>
                                    <button type="submit" style="margin-left: 10px; padding: 2px 10px" class="btn btn-default">Submit</button>
                                </form>
                            </div>

                            <div class="text-right pull-right col-xs-2">Total: {{ $licenses->total() }}</div>
                        </div>
                    </div>
                    {{--licenses table--}}
                    <table class="table table-condensed phonex-table-sortable">
                        <tr>
                            <th >{!! link_to_sort('id', 'ID') !!}</th>
                            <th >{!! link_to_sort('username', 'Username') !!}</th>
                            <th>{!! link_to_sort('license_type', 'License type') !!}</th>
                            <th>{!! link_to_sort('is_trial', 'Trial') !!}</th>
                            <th>Active</th>
                            <th>{!! link_to_sort('starts_at', 'Start date') !!}</th>
                            <th>{!! link_to_sort('expires_at', 'Expiration date') !!}</th>
                            <th width="25%">Comment</th>
                            <th>Options</th>
                        </tr>
                        @foreach($licenses as $lic)
                            <tr>
                                <td>{{ $lic->id}}</td>
                                <td><a href="{{ \URL::route('users.show', [$lic->user_id]) }}">{{ $lic->username }}</a></td>
                                <td>{{ ucfirst($lic->license_type) }}</td>
                                <td>@if($lic->is_trial) Yes @else No @endif</td>
                                <td>@if($lic->active) Yes @else No @endif</td>
                                <td>{{ $lic->formatted_starts_at}}</td>
                                <td>{{ $lic->formatted_expires_at }}</td>
                                <td>{{ $lic->comment }}</td>

                                <td>
                                    <div class="btn-group  btn-group-xs">
                                        <a type="button" class="btn btn-info view-btn-edit" href="{{ \URL::route('licenses.edit', $lic->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>

                </div>

                <div class="text-center">
                    {!! $licenses->appends(Request::except('page'))->render(); !!}
                </div>
            </div>
        </div>


    </section>

</div>

@endsection
