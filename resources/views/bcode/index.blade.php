@extends('app')

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-sm-12">
                <h1>
                    Business codes
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
                <div style="margin-bottom: 10px" class="phonex-table-div clearfix">
                    <div class="left-cell">
                    </div>
                    <div class="right-cell">
                        {{--<a class="btn btn-sm btn-primary view-btn-create" href="/users/create">--}}
                           {{--Export MP codes--}}
                        {{--</a>--}}
                        <a class="btn btn-sm btn-primary view-btn-create" href="/bcodes/generate-mp-codes/">
                            <i class="fa fa-plus-circle"></i> Generate MP codes
                        </a>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="text-right pull-right col-xs-2">Total: {{ $bcodes->total() }}</div>
                        </div>
                    </div>
                    <table class="table table-condensed phonex-table-sortable">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Group</th>
                            <th>Exported</th>
                            <th>Licenses limit</th>
                            <th>Licenses acquired</th>
                            <th class="text-center">Options</th>
                        </tr>
                        @foreach($bcodes as $bcode)
                            <tr>
                                <td>{{ $bcode->id }}</td>
                                <td>{{ $bcode->code }}</td>
                                <td>{{ $bcode->group->name }}</td>
                                <td>@if($bcode->exported) Yes @else No @endif</td>

                                <td>{{ $bcode->licenses_limit  }}</td>
                                <td>...</td>
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
                    {!! $bcodes->appends(Request::except('page'))->render(); !!}
                </div>
            </div>
        </div>

    </section>
@endsection
