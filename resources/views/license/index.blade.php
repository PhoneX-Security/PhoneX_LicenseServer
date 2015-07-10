@extends('content-with-header')

@section('title', 'Licenses')
@section('subtitle', 'Manage')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">

                <h3 class="box-title">Licenses</h3>
                {{--<div class="box-tools">--}}


                    {{--TODO: Rework--}}
                    {{--<form method="get" class="form-inline">--}}
                        {{--@if(\Request::has('o')) <input type="hidden" name="o" value="{{ InputGet::get('o') }}" /> @endif--}}
                        {{--@if(\Request::has('s')) <input type="hidden" name="s" value="{{ InputGet::get('s') }}" /> @endif--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="col-md-1 control-label text-le">Filters:</label>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="checkbox-inline" for="checkboxes-0">--}}
                                {{--{!! Form::checkbox('active_only', '1', \Request::has('active_only'), ['id'=>'checkboxes-0']); !!}--}}
                                {{--Active only--}}
                            {{--</label>--}}

                            {{--<label class="checkbox-inline" for="checkboxes-1">--}}
                                {{--{!! Form::checkbox('trial_only', '1', \Request::has('trial_only'), ['id'=>'checkboxes-1']); !!}--}}
                                {{--Trial only--}}
                            {{--</label>--}}

                        {{--</div>--}}
                        {{--<button type="submit" class="btn btn-default">Submit</button>--}}
                    {{--</form>--}}
                {{--</div>--}}
            </div>




            <div class="box-body">

                <div class="row form-inline" style="margin-bottom: 5px">
                    <div class="col-sm-6">
                        <form class="form-horizontal" action="{{ route('licenses.index') }}" method="get">
                            <div class="input-group">
                                <input type="search" class="form-control input-sm" name="username" value="{{ Input::get('username') }}" placeholder="Search">
                            <span class="input-group-btn">
                                <button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                            </span>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6 text-right">
                    </div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th >{!! link_to_sort('id', 'ID') !!}</th>
                            <th >{!! link_to_sort('username', 'Username') !!}</th>
                            <th>{!! link_to_sort('license_type', 'Expiration') !!}</th>
                            <th>{!! link_to_sort('license_func_type', 'Type') !!}</th>
                            <th>Active</th>
                            <th>{!! link_to_sort('starts_at', 'Start date') !!}</th>
                            <th>{!! link_to_sort('expires_at', 'Expiration date') !!}</th>
                            <th width="22%">Comment</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($licenses as $lic)
                        <tr>
                            <td>{{ $lic->id}}</td>
                            <td><a href="{{ \URL::route('users.show', [$lic->user_id]) }}">{{ $lic->username }}</a></td>
                            <td>{{ ucfirst($lic->license_type) }}</td>
                            <td>{{ ucfirst($lic->license_func_type) }}</td>
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
                    </tbody>
                </table>

            </div>

            {{--<div class="box-footer clearfix">--}}
                {{--<div class="pull-left">--}}
                    {{--<div class="dataTables_info" id="example2_info" role="status" aria-live="polite">--}}
                        {{--Total {{ $licenses->total() }} entries</div>--}}
                {{--</div>--}}

                {{--<div class="pull-right">--}}
                    {{--{!! $licenses->appends(Request::except('page'))->render(); !!}--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>


    </section>
@endsection
