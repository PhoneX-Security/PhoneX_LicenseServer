@extends('content-with-header')

@section('title', 'User: '. $user->username)
{{--@section('subtitle', 'Manage')--}}

@section('content')
    @parent

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

            @include('errors.notifications')

            @include('user.chips.top-nav')

            <div class="box box-default">
                <div class="box-header with-border">

                    <div class="row form-inline" style="margin-bottom: 5px">
                        <div class="col-sm-6">
                            <a href="{{ route('users.edit', [$user->id]) }}" class="btn btn-primary">Edit</a>
                            <a class="btn btn-default" href="#" data-href="#" data-toggle="modal" data-target="#modal-reset-trial-counter">Reset trial counter</a>
                            <a href="{{ route('users.push-lic-update', [$user->id]) }}" class="btn btn-default">Push license update</a>
                            <a class="btn btn-danger" href="#" data-href="#" data-toggle="modal" data-target="#modal-force-logout">Logout</a>
                        </div>

                        <div class="col-sm-6 text-right">
                        </div>
                    </div>
                </div>

                @include('dialogs.user-reset-trial-counter')
                @include('dialogs.user-force-logout')

                <div class="box-body">
                    @include('user.chips.user-details', ['user' => $user])
                </div>
            </div>
		</div>
	</div>


	</section>

@endsection
