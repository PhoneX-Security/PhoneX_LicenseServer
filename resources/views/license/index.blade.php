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
            </div>

            <div class="box-body">
                <div class="row form-inline" style="margin-bottom: 5px">
                    <div class="col-sm-6">
                        <form class="form-inline inline-block" action="{{ route('licenses.index') }}" method="get">
                            <div class="input-group">
                                <input type="search" class="form-control input-sm" name="username" value="{{ Input::get('username') }}" placeholder="Search by username">
                            <span class="input-group-btn">
                                <button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                            </span>
                            </div>
                        </form>

                        <form class="form-inline inline-block" style="margin-left: 5px; margin-right: 5px" method="get">

                            @if(\Request::has('o')) <input type="hidden" name="o" value="{{ InputGet::get('o') }}" /> @endif
                            @if(\Request::has('s')) <input type="hidden" name="s" value="{{ InputGet::get('s') }}" /> @endif

                            <div class="form-group" style="margin-left: 5px; margin-right: 5px">
                                <label for="input-limit">Limit:</label>
                                <input type="number" style="width: 70px" class="form-control" id="input-limit" name="limit" value="{{ request('limit', 20)}}">
                            </div>

                            <div class="form-group" style="margin-left: 5px; margin-right: 5px">
                                <label class="checkbox-inline" for="checkboxes-0">
                                    {!! Form::checkbox('active_only', '1', \Request::has('active_only'), ['id'=>'checkboxes-0']); !!}
                                    Active only
                                </label>
                            </div>

                            <button type="submit" class="btn btn-default">Submit</button>
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
                            <th>License code (code/group)</th>
                            <th>Active</th>
                            <th>{!! link_to_sort('starts_at', 'Starts at') !!}</th>
                            <th>{!! link_to_sort('expires_at', 'Expires at') !!}</th>
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
                            <td>
                                @if($lic->business_code_id)
                                    {{$lic->businessCode->printable_code }} /
                                    {{ $lic->businessCode->getGroup()->name or 'unknown-group' }}
                                @else - @endif
                            </td>
                            <td>@if($lic->active) Yes @else No @endif</td>
                            <td>{{ $lic->starts_at}}</td>
                            <td>{{ $lic->expires_at }}</td>
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

            <div class="box-footer clearfix">
                <div class="pull-left">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                        Total {{ $licenses->total() }} entries</div>
                </div>

                <div class="pull-right">
                    {!! $licenses->appends(Request::except('page'))->render() !!}
                </div>
            </div>
        </div>


    </section>
@endsection
