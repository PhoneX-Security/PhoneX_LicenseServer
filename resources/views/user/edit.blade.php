@extends('content-with-header')

@section('title', 'User: '. $user->username)
@section('subtitle', 'Edit')

@section('content')
    @parent

    <section class="content">

        <div class="row">
            <div class="col-sm-12">

                @include('errors.notifications')

                @include('dialogs.reset-password')

                <div class="box box-default">
                    <div class="box-header with-border">

                        <div class="row form-inline" style="margin-bottom: 5px">
                            <div class="col-sm-6">
                                <a class="btn btn-primary" href="{{ \URL::route('users.show', [$user->id]) }}">
                                    <i class="fa fa-close"></i> Cancel editing
                                </a>
                                <a class="btn btn-default" href="#"
                                   data-href="#" data-toggle="modal" data-target="#reset-password">Reset password</a>
                            </div>

                            <div class="col-sm-6 text-right">
                            </div>
                        </div>
                    </div>


                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4 ">
                                <dl>
                                    <dt>Username</dt>
                                    <dd>{{ $user->username }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-4 ">
                                <dl>
                                    <dt>Email Address</dt>
                                    <dd>{{ $user->email or '-'}}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>
@endsection