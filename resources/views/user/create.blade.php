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
				<ol class="breadcrumb">
					<li><a href="#"><i class="fa fa-home"></i> Home </a></li>
					<li class="active">Users</li>
				</ol>
			</div>
		</div>
	</section>

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

			<div class="phonex-table-div clearfix">
				<div class="left-cell">
					<h2>New user</h2>
				</div>
				<div class="right-cell">

					<a class="btn btn-sm btn-info view-btn-create" href="#">
						<i class="fa fa-angle-left"></i> Back
					</a>

					{{--<button class="btn btn-small"><i class="icon-pencil"></i> Edit</button>--}}
				</div>
			</div>

			{{--<!-- Nav tabs -->--}}
			{{--<ul class="nav nav-tabs" role="tablist">--}}
				{{--<li class="active"><a href="#home" role="tab" data-toggle="tab">Home</a></li>--}}
				{{--<li><a href="#profile" role="tab" data-toggle="tab">Profile</a></li>--}}
				{{--<li><a href="#messages" role="tab" data-toggle="tab">Messages</a></li>--}}
				{{--<li><a href="#settings" role="tab" data-toggle="tab">Settings</a></li>--}}
			{{--</ul>--}}

			{{--<!-- Tab panes -->--}}
			{{--<div class="tab-content">--}}
				{{--<div class="tab-pane active" id="home">A</div>--}}
				{{--<div class="tab-pane" id="profile">B</div>--}}
				{{--<div class="tab-pane" id="messages">C</div>--}}
				{{--<div class="tab-pane" id="settings">D</div>--}}
			{{--</div>--}}

			<div class="panel panel-default">
				<div class="panel-body">
					<div class="box-body">

						<div class="row">
							<div class="col-md-6 ">
								<div class="form-group"><label for="username" class="control-label">Username</label>
									<input class="form-control" placeholder="Enter username" id="username" type="text" name="username">
									<span class="help-block">Username serves as an login into PhoneX application.</span>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 ">
								<div class="form-group"><label for="email" class="control-label">E-mail Address</label>
									<input class="form-control" placeholder="Enter E-mail Address in format username@phone-x.net" id="email" type="email" name="email">

								</div>
							</div>
						</div>

						{{--<div class="row">--}}
						{{--<div class="col-md-6 ">--}}
								{{--<div class="form-group"><label for="first_name" class="control-label">First Name</label><input class="form-control" placeholder="Enter First Name" id="first_name" type="text" name="first_name"></div>--}}
							{{--</div>--}}
							{{--<div class="col-md-6 ">--}}
								{{--<div class="form-group"><label for="last_name" class="control-label">Last Name</label><input class="form-control" placeholder="Enter Last Name" id="last_name" type="text" name="last_name"></div>--}}
							{{--</div>--}}
						{{--</div>--}}

						<div class="checkbox">
							<label>
								<input id="users_create_has_access" type="checkbox"> Give admin access
							</label>
						</div>

						<div class="row">
							<div class="col-md-6 ">
								<div class="form-group">
									<label for="password" class="control-label">Password</label>
									<input class="form-control" placeholder="Enter Password" id="users_create_password" type="password" name="password" disabled>
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
					<div class="row">
						<div class="col-md-12">
							<div><input class="btn-large btn-primary btn" type="submit" value="Submit"> <input class="btn-large btn-default btn" type="reset" value="Reset"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	</section>

</div>


@endsection
