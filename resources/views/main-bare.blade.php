<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PhoneX Backend | @yield('title', 'Homepage')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link href="/dist/css/skins/skin-yellow-light.min.css" rel="stylesheet" type="text/css" />

    @yield('js-scripts')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

{{--BODY TAG OPTIONS:--}}
{{--=================--}}
{{--Apply one or more of the following classes to get the--}}
{{--desired effect--}}
{{--|---------------------------------------------------------|--}}
{{--| SKINS         | skin-blue                               |--}}
{{--|               | skin-black                              |--}}
{{--|               | skin-purple                             |--}}
{{--|               | skin-yellow                             |--}}
{{--|               | skin-red                                |--}}
{{--|               | skin-green                              |--}}
{{--|---------------------------------------------------------|--}}
{{--|LAYOUT OPTIONS | fixed                                   |--}}
{{--|               | layout-boxed                            |--}}
{{--|               | layout-top-nav                          |--}}
{{--|               | sidebar-collapse                        |--}}
{{--|               | sidebar-mini                            |--}}
{{--|---------------------------------------------------------|--}}

<body class="login-page">

@yield('content')

<!-- jQuery 2.1.4 -->
<script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="/dist/js/app.min.js" type="text/javascript"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience. Slimscroll is required when using the
      fixed layout. -->
</body>
</html>