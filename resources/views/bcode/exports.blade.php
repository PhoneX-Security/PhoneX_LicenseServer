@extends('content-with-header')

@section('title', 'Business codes')
@section('subtitle', 'Exports')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">List of exports</h3>
                <div class="box-tools">
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Creator</th>
                            <th>Created at</th>
                            <th>Exported to email</th>
                            <th class="text-center">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td><a href="{{ route('users.show', $item->creator->id) }}">{{ $item->creator->username }}</a></td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->email }}</td>
                            <td class="text-center">
                                <div class="btn-group  btn-group-xs">
                                    <a type="button" class="btn btn-info view-btn-edit" href="{{ "/bcodes/export/" . $item->id }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Details</a>
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
                        {{--Total {{ $bcodes->total() }} entries</div>--}}
                {{--</div>--}}

                {{--<div class="pull-right">--}}
                    {{--{!! $bcodes->appends(Request::except('page'))->render(); !!}--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>

    </section>
@endsection
