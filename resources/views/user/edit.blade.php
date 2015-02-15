@extends('app')

@section('content')

<div class="container">

	<section class="content-header">
		<div class="row">
			<div class="col-sm-12 pull-left">
				<h1>
					Users
					{{--<small>New user</small>--}}
				</h1>
                @include('navigation.breadcrumb')
			</div>
		</div>
	</section>

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

			<div class="phonex-table-div clearfix">
				<div class="left-cell">
					<h2>User {{ $user->username }}</h2>
				</div>
				<div class="right-cell">

					<a class="btn btn-sm btn-primary view-btn-create" href="{{ \URL::route('users.show', [$user->id]) }}">
						<i class="fa fa-close"></i> Cancel editing
					</a>
                    <a class="btn btn-sm btn-primary" href="/users">
                        <i class="fa fa-edit"></i> Issue license
                    </a>

				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">

                    @include('errors.notifications')

                    {!! \Form::model($user, array('method' => 'patch', 'route' => array('users.update', $user->id))) !!}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <h4>Details</h4>

                        <div class="row">
                            <div class="col-md-6 ">
                                <dl>
                                    <dt>Username</dt>
                                    <dd>{{ $user->username }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6 ">
                                <dl>
                                    <dt>Email Address</dt>
                                    <dd>{{ $user->email or '-'}}</dd>
                                </dl>
                            </div>
                        </div>

                        <h4>Access</h4>
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <div class="checkbox">
                                    <label>
                                        {{--<input id="users_create_has_access" name="has_access" @if(old('has_access')) checked @endif type="checkbox"> Give access--}}
                                        {!! \Form::checkbox('has_access'); !!} Give access
{{--                                        <input id="users_create_has_access" name="has_access" @if(old('has_access')) checked @endif type="checkbox"> Give access--}}
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label for="password" class="control-label">New password</label>
                                            <input class="form-control" placeholder="Enter Password" id="users_create_password" type="password" name="password">
                                            <span class="help-block">Fill out if you want to assign a new password.</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label for="password_confirmation" class="control-label">Confirm Password</label>
                                            <input class="form-control" placeholder="Re-enter Password" id="users_create_password_confirmation" type="password" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div><input class="btn-large btn-primary btn" type="submit" value="Update"></div>
                            </div>
                        </div>
                    {!! \Form::close() !!}
				</div>
			</div>
		</div>
	</div>


	</section>

</div>


@endsection
