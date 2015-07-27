@extends('form.form-create')
@section('title', 'License #' . $license->id)
@section('subtitle', 'Edit')

@section('form-nav')
    <div class="row form-inline" style="margin-bottom: 5px">
        <div class="col-sm-6">
            <a class="btn btn-sm btn-primary view-btn-create" href="/licenses">
                <i class="fa fa-angle-left"></i> Back to licenses
            </a>
        </div>

        <div class="col-sm-6 text-right">
        </div>
    </div>
@endsection

@section('form')
    {!! \Form::model($license, array('method' => 'patch', 'route' => array('licenses.update', $license->id))) !!}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <h4>Details</h4>

    <div class="row">
        <div class="col-md-2">
            <dl>
                <dt>Issued to</dt>
                <dd><a href="{{ \URL::route('users.show', $license->user_id) }}">{{ $license->user->username }}</a></dd>
            </dl>
        </div>
        <div class="col-md-2 ">
            <dl>
                <dt>Expiration</dt>
                <dd>{{ $license->licenseType->readableType() }}</dd>
            </dl>
        </div>
        <div class="col-md-2">
            <label class="control-label">Type</label>
            <select name="license_func_type_id" class="form-control">
                @foreach($licenseFuncTypes as $type)
                    <option @if($type->selected) selected @endif value="{{ $type->id }}">{{ ucfirst($type->name) }} </option>
                @endforeach
            </select>
        </div>
        {{--<div class="col-md-2 ">--}}
            {{--<dl>--}}
                {{--<dt>Type</dt>--}}
                {{--<dd>{{ ucfirst($license->licenseFuncType->name) }}</dd>--}}
            {{--</dl>--}}
        {{--</div>--}}
    </div>

    <div class="row">
        <div class="col-md-2 ">
            <dl>
                <dt>Active</dt>
                <dd>@if($license->isActive()) Yes @else No @endif</dd>
            </dl>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 ">
            <dl>
                <dt>Start date</dt>
                <dd>{{ date_simple($license->starts_at) }}</dd>
            </dl>
        </div>
        <div class="col-md-2 ">
            <dl>
                <dt>Expire date</dt>
                <dd>{{ date_simple($license->expires_at) }}</dd>
            </dl>
        </div>

        <div class="col-md-2 ">
            <dl>
                <dt>Issuer</dt>
                <dd>
                    @if($license->issuer)
                        <a href="{{route('users.show', [$license->issuer->id])}}">{{$license->issuer->username}}</a>
                    @endif
                </dd>
            </dl>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 ">
            <div class="form-group">
                <label for="password" class="control-label">License notes</label>
                {!! \Form::textarea('comment', null, ['rows'=>'8', 'class'=> 'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div><input class="btn-large btn-primary btn" type="submit" value="Update"></div>
        </div>
    </div>
    {!! \Form::close() !!}
@endsection