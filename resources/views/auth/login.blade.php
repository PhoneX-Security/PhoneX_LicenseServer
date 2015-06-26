@extends('main-bare')

@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="/"><b>Admin</b>LTE</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">

            <form role="form" method="POST" action="/auth/login">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="Password"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div><!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div><!-- /.col -->
                </div>
            </form>

            {{--<div class="social-auth-links text-center">--}}
                {{--<p>- OR -</p>--}}
                {{--<a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>--}}
                {{--<a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>--}}
            {{--</div><!-- /.social-auth-links -->--}}

            <a href="/password/email">I forgot my password</a><br>
            {{--<a href="register.html" class="text-center">Register a new membership</a>--}}

        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

{{--<div class="container-fluid">--}}
	{{--<div class="row">--}}
		{{--<div class="col-md-8 col-md-offset-2">--}}
			{{--<div class="panel panel-default">--}}
				{{--<div class="panel-heading">Login</div>--}}
				{{--<div class="panel-body">--}}
					{{--@include('errors.notifications')--}}

					{{--<form class="form-horizontal" role="form" method="POST" action="/auth/login">--}}
						{{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}

						{{--<div class="form-group">--}}
							{{--<label class="col-md-4 control-label">E-Mail Address</label>--}}
							{{--<div class="col-md-6">--}}
								{{--<input type="email" class="form-control" name="email" value="{{ old('email') }}">--}}
							{{--</div>--}}
						{{--</div>--}}

						{{--<div class="form-group">--}}
							{{--<label class="col-md-4 control-label">Password</label>--}}
							{{--<div class="col-md-6">--}}
								{{--<input type="password" class="form-control" name="password">--}}
							{{--</div>--}}
						{{--</div>--}}

						{{--<div class="form-group">--}}
							{{--<div class="col-md-6 col-md-offset-4">--}}
								{{--<div class="checkbox">--}}
									{{--<label>--}}
										{{--<input type="checkbox" name="remember"> Remember Me--}}
									{{--</label>--}}
								{{--</div>--}}
							{{--</div>--}}
						{{--</div>--}}

						{{--<div class="form-group">--}}
							{{--<div class="col-md-6 col-md-offset-4">--}}
								{{--<button type="submit" class="btn btn-primary" style="margin-right: 15px;">--}}
									{{--Login--}}
								{{--</button>--}}

								{{--<a href="/password/email">Forgot Your Password?</a>--}}
							{{--</div>--}}
						{{--</div>--}}
					{{--</form>--}}
				{{--</div>--}}
			{{--</div>--}}
		{{--</div>--}}
	{{--</div>--}}
{{--</div>--}}
@endsection
