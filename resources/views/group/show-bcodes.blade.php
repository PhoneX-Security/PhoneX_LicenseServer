@extends('group.show')

@section('options')
    <div class="row form-inline" style="margin-bottom: 5px">
        <div class="col-sm-6">
            {{--<a href="{{ route('users.index', [ 'user_group[]' => $group->id ]) }}" class="btn btn-default">Show in users list</a>--}}
        </div>

        <div class="col-sm-6 text-right">
        </div>
    </div>
@endsection

@section('details')
    @include('bcode.chips.bcodes-table', ['bcodes' => $group->bcodes])
@endsection
