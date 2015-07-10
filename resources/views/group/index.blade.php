@extends('content-with-header')

@section('title', 'Groups')
@section('subtitle', 'Manage')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Groups</h3>
                <div class="box-tools">
                    <a class="btn btn-sm btn-primary" href="{{ route('groups.create') }}">
                        <i class="fa fa-plus-circle"></i> New Group
                    </a>
                </div>
            </div>
            <div class="box-body">

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Group name</th>
                        <th>Owner</th>
                        <th>Comment</th>
                        <th>Users count</th>
                        <th class="text-center">Options</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td>{{ $group->id }}</td>
                            <td><a href="{{ \URL::route('users.index', [ 'user_group[]' => $group->id ]) }}">{{ $group->name or '' }}</a></td>
                            <td>
                                @if($group->owner)
                                    <a href="{{ route('users.show', [ $group->owner->id ]) }}">{{ $group->owner->username }}</a>
                                @endif
                            </td>
                            <td>{{ $group->comment }}</td>
                            <td>{{ $group->users()->count()  }}</td>
                            <td class="text-center">
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-left">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                        Total {{ $groups->total() }} entries</div>
                </div>

                <div class="pull-right">
                    {!! $groups->appends(Request::except('page'))->render(); !!}
                </div>
            </div>
        </div>

    </section>
@endsection
