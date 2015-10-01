@extends('content-with-header')

@section('title', 'Export: #'. $export->id)
{{--@section('subtitle', 'Manage')--}}

@section('content')
    @parent

    <section class="content">

        <div class="row">
            <div class="col-sm-12">

                @include('errors.notifications')

                <div class="box box-default">
                    <div class="box-header with-border">

                        {{--<div class="row form-inline" style="margin-bottom: 5px">--}}
                            {{--<div class="col-sm-6">--}}
                                {{--<a href="{{ \URL::route('users.edit', [$user->id]) }}" class="btn btn-primary">Edit</a>--}}
                            {{--</div>--}}

                            {{--<div class="col-sm-6 text-right">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Created at</dt>
                                    <dd>{{ $export->created_at }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Created by</dt>
                                    <dd>@if($export->creator)<a href="{{ route('users.show', [$export->creator->id]) }}">{{ $export->creator->username }}</a>@endif</dd>
                                </dl>
                            </div>
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Exported to mail</dt>
                                    <dd>{{ $export->email }}</dd>
                                </dl>
                            </div>
                        </div>

                        @if($firstCode)
                        <div class="row">
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Product</dt>
                                    <dd>{{$export->product->uc_name}}</dd>
                                </dl>
                            </div>
                            {{--<div class="col-md-2 ">--}}
                                {{--<dl>--}}
                                    {{--<dt>Type</dt>--}}
                                    {{--<dd>--}}
                                        {{--@if($export->licenseFuncType)--}}
                                            {{--{{$export->licenseFuncType->uc_name}}--}}
                                        {{--@else--}}
                                            {{--{{ $firstCode->licenseFuncType->uc_name }}--}}
                                        {{--@endif--}}
                                    {{--</dd>--}}
                                {{--</dl>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2 ">--}}
                                {{--<dl>--}}
                                    {{--<dt>Expiration</dt>--}}
                                    {{--<dd>--}}
                                        {{--@if($export->LicenseType)--}}
                                            {{--{{$export->LicenseType->uc_name}}--}}
                                        {{--@else--}}
                                            {{--{{ $firstCode->licenseType->uc_name_with_days }}--}}
                                        {{--@endif--}}

                                    {{--</dd>--}}
                                {{--</dl>--}}
                            {{--</div>--}}
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Group</dt>
                                    <dd>
                                        @if($export->group)
                                            <a href="{{route('groups.show', $export->group->id)}}">{{$export->group->name}}</a>
                                        @elseif($firstCode->group)
                                            <a href="{{route('groups.show', $firstCode->group->id)}}">{{$firstCode->group->name}}</a>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Parent user</dt>
                                    <dd>
                                        @if($export->parent)
                                            <a href="{{ route('users.show', [$export->parent->id]) }}">{{ $export->parent->username }}</a>
                                        @elseif($firstCode->parent)
                                            <a href="{{ route('users.show', [$firstCode->parent->id]) }}">{{ $firstCode->parent->username }}</a>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Comment</dt>
                                    <dd>{{ $export->comment }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                @if(isset($codePairs))
                                    @include('bcode.chips.code-pairs-table', compact('codePairs'))
                                @elseif(isset($codes))
                                    @include('bcode.chips.single-codes-table', compact('codes'))
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </section>

@endsection
