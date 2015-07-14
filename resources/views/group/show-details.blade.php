@extends('group.show')

@section('options')
    <div class="row form-inline" style="margin-bottom: 5px">
        <div class="col-sm-6">
            <a href="{{ route('groups.edit', [$group->id]) }}" class="btn btn-primary">Edit</a>
        </div>

        <div class="col-sm-6 text-right">
        </div>
    </div>
@endsection

@section('details')
    <h4>Group details</h4>

    <div class="row">
        <div class="col-md-2 ">
            <dl>
                <dt>Name</dt>
                <dd>{{ $group->name}}</dd>
            </dl>
        </div>
        <div class="col-md-2 ">
            <dl>
                <dt>Created at</dt>
                <dd>{{ $group->created_at }}</dd>
            </dl>
        </div>
        <div class="col-md-2 ">
            <dl>
                <dt>Group owner</dt>
                <dd>
                    @if($group->owner)
                        <a href="{{ route('users.show', $group->owner->id) }}">{{ $group->owner->username or ''}}</a>
                    @endif

                </dd>
            </dl>
        </div>
    </div>

    {{--<div class="row">--}}
        {{--<div class="col-md-2">--}}

        {{--</div>--}}
    {{--</div>--}}

    <div class="row">
        <div class="col-md-2 ">
            <dl>
                <dt>Comment</dt>
                <dd>{{ $group->comment }}</dd>
            </dl>
        </div>

    </div>

@endsection
