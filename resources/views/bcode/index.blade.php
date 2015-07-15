@extends('content-with-header')

@section('title', 'Business codes')
@section('subtitle', 'Manage')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Business codes</h3>
                <div class="box-tools">
                    {{--<a class="btn btn-sm btn-primary view-btn-create" href="/bcodes/generate-single-codes/">--}}
                        {{--<i class="fa fa-plus-circle"></i> New single codes--}}
                    {{--</a>--}}

                    <form class="form-inline inline-block" action="/bcodes" method="get">

                        <div class="input-group">
                            <input type="text" name="code" value="{{Request::get('code')}}" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search by code">
                            <div class="input-group-btn">
                                <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <a class="btn btn-sm btn-primary view-btn-create" href="/bcodes/generate-code-pairs/">
                        <i class="fa fa-plus-circle"></i> New code pairs
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th><abbr data-toggle="tooltip" data-placement="top" title="Users who are created with associated business codes are automatically paired as contacts in their contact lists. They are also known as 'code pairs'.">Associated code(s)</abbr></th>
                            <th>Group</th>
                            <th>Parent user</th>
                            <th>Users limit</th>
                            <th>Users created</th>
                            <th class="text-center">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($bcodes as $bcode)
                        <tr>
                            <td>{{ $bcode->id }}</td>
                            <td>{{ $bcode->code }}</td>
                            <td>
                                @if($bcode->clMappings)
                                    @foreach($bcode->clMappings as $k => $assocCode)
                                        {{ $assocCode->code }}
                                        @if($k != 0), @endif
                                    @endforeach
                                @else -
                                @endif </td>
                            </td>
                            <td>{{ $bcode->group->name or ''}}</td>
                            <td>{{ $bcode->parent->username or ''}}</td>
                            <td>{{ $bcode->users->count() . '/' . $bcode->users_limit  }}</td>
                            <td>@if($bcode->users)
                                    @foreach($bcode->users as $k => $user)
                                        <a href="{{route('users.show',$user->id)}}">{{ $user->username }}</a>
                                        @if($k != 0), @endif
                                    @endforeach
                                @else -
                                @endif </td>
                            <td class="text-center">
                                <div class="btn-group  btn-group-xs">
                                    {{--<a type="button" class="btn btn-info view-btn-edit" href="{{ \URL::route('users.edit', $user->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>--}}
                                    {{--<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="#" title="Delete user"><i class="fa fa-times-circle-o"></i></a>--}}
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
                        Total {{ $bcodes->total() }} entries</div>
                </div>

                <div class="pull-right">
                    {!! $bcodes->appends(Request::except('page'))->render() !!}
                </div>
            </div>
        </div>

    </section>
@endsection
