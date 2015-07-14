@extends('form.form-create')
@section('title', 'Group #' . $group->id)
@section('subtitle', 'Edit')

@section('form-nav')
    <div class="row form-inline" style="margin-bottom: 5px">
        <div class="col-sm-6">
            <a class="btn btn-sm btn-primary view-btn-create" href="{{ route('groups.show', $group->id) }}">
                <i class="fa fa-angle-left"></i> Cancel editing
            </a>
        </div>

        <div class="col-sm-6 text-right">
        </div>
    </div>
@endsection

@section('form')
    {!! \Form::open(['method' => 'put', 'route' => array('groups.update', $group->id)]) !!}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="row">
        <div class="col-md-2">
            <dl>
                <dt>Name</dt>
                <dd>
                    <input class="form-control" type="text" name="name" value="{{old('name', $group->name)}}" />
                </dd>
            </dl>
        </div>
        <div class="col-md-2 ">
            <dl>
                <dt>Owner (username)</dt>
                <dd>
                    <input class="form-control" type="text" name="owner_username" value="{{old('owner_username', $group->owner ? $group->owner->username : '' )}}" />
                </dd>
            </dl>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 ">
            <dl>
                <dt>Comment</dt>
                <dd>
                    <textarea cols="8" name="comment" class="form-control">{{old('comment', $group->comment)}}</textarea>
                </dd>
            </dl>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div><input class="btn-large btn-primary btn" type="submit" value="Update"></div>
        </div>
    </div>
    {!! \Form::close() !!}
@endsection