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
                                    <dd><a href="{{ route('users.show', [$export->creator->id]) }}">{{ $export->creator->username }}</a></dd>
                                </dl>
                            </div>
                            <div class="col-md-2 ">
                                <dl>
                                    <dt>Exported to mail</dt>
                                    <dd>{{ $export->email }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                @include('bcode.chips.code-pairs-table', ['codePairs' => $codePairs])
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </section>

@endsection
