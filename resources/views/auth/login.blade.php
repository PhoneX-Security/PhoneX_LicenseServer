@extends('main-bare')

@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="/">PhoneX <b>Admin</b></a>
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
                <div class="row" style="margin-left: 5px">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div><!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Log In</button>
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
@endsection
