<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PhoneX Web Console</title>

	<link rel="stylesheet" href="/css/font-awesome.min.css">
	<link href="/css/app.css" rel="stylesheet"> <!-- Normalize stylesheet -->
	<link href="/css/datepicker.css" rel="stylesheet">

	<link href="/css/custom.css" rel="stylesheet">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- Scripts -->
	<script src="/js/jquery-2.1.3.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="/js/bootstrap-datepicker.js"></script>

    <!-- Bootstrap multiselect -->
    <script type="text/javascript" src="js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>

	{{--<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->--}}
	{{--<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->--}}
	{{--<!--[if lt IE 9]>--}}
		<!--<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>-->
		<!--<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>-->
	{{--<![endif]-->--}}
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" style="margin-top: -12px;" href="/">
					<img alt="Brand" height="44px" src="/img/icon4.png" style="display:inline">
                    License Server {{ Config::get('app.version_name') }}
				</a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


				<ul class="nav navbar-nav navbar-right">
					@if (Auth::guest())
						<li><a href="/login">Login</a></li>
                        {{--Do not allow registration at the moment--}}
						{{--<li><a href="/auth/register">Register</a></li>--}}
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{  Auth::user()->email }} <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="/logout">Logout</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>
	</nav>

    @if (Auth::check())
    <div class="container-fluid">
        <div class="row ">
            <div class="col-sm-3 col-md-2 nav-side-menu">
                @include('navigation.sidebar')
            </div>

            <div class="col-sm-9 col-md-9 main">
                @yield('content')
            </div>
        </div>
    </div>
    @else
        <div class="container">
            @yield('content')
        </div>
    @endif

	<footer class="panel-footer navbar-bottom">
		<div class="container-fluid">
			&copy; 2015 PhoneX Security <i class="icon-large icon-search"></i>
		</div>
	</footer>

    <script src="/js/custom.js"></script>

</body>
</html>
