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

					<a class="btn btn-sm btn-info view-btn-create" href="/users">
						<i class="fa fa-angle-left"></i> Back
					</a>

				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>No!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form  role="form" method="POST" action="/users">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<h4>Details</h4>

						<div class="row">
							<div class="col-md-6 ">
								<div class="form-group"><label for="username" class="control-label">Username*</label>
									<input class="form-control" required value="{{ old('username') }}" placeholder="Enter username" id="username" type="text" name="username">
									<span class="help-block">Username serves as an login into PhoneX application.</span>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 ">
								<div class="form-group"><label for="email" class="control-label">E-mail Address*</label>
									<input class="form-control" required value="{{ old('email') }}" placeholder="Enter E-mail Address in format username@phone-x.net" id="email" type="email" name="email">

								</div>
							</div>
						</div>



						<h4>Access</h4>
						<div class="panel panel-default">
							<div class="panel-body">

								<div class="checkbox">
									<label>
										<input id="users_create_has_access" name="has_access" @if(old('has_access')) checked @endif type="checkbox"> Give access
									</label>
								</div>

								<div class="row">
									<div class="col-md-6 ">
										<div class="form-group">
											<label for="password" class="control-label">Password</label>
											<input class="form-control" placeholder="Enter Password" id="users_create_password" type="password" name="password">
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

						<h4>License</h4>
						<div class="panel panel-default">
							<div class="panel-body">

								<div class="checkbox">
									<label>
										<input id="users_create_issue_license" name="issue_license" @if(old('issue_license')) checked @endif type="checkbox"> Issue license
									</label>
								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label for="starts_at" class="control-label">Start date</label>
												<div class="input-group date">
													<input value="{{ old('starts_at') }}" type="text" name="starts_at" class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
												</div>
											</div>
										</div>

										<div class="col-md-3">
											<label for="password" class="control-label">Type</label>
											<select name="license_type_id" class="form-control">
												@foreach($licenseTypes as $type)
													<option value="{{ $type->id }}">{{ ucfirst($type->name) . " (" . $type->days . " days)" }} </option>
												@endforeach
											</select>
										</div>

										<div class="col-md-6 ">
											<div class="form-group"><label for="opensips_password" class="control-label">PhoneX default password</label>
												<input class="form-control" value="{{ old('phonex_default_password') }}" id="phonex_default_password" type="text" name="phonex_default_password">
												<span class="help-block">Password will be changed on first login.</span>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6 ">
										<div class="form-group">
											<label for="password" class="control-label">License notes</label>
											<textarea class="form-control" rows="3"></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>

						<p>* Required fields</p>


					<div class="row">
						<div class="col-md-12">
							<div><input class="btn-large btn-primary btn" type="submit" value="Submit"> <input class="btn-large btn-default btn" type="reset" value="Reset"></div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	</section>

</div>


@endsection
