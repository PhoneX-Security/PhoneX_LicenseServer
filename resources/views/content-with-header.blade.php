@extends('main')

@section('content')

    {{--<div class="panel panel-default">--}}
        {{--<div class="panel-heading">Home</div>--}}

        {{--<div class="panel-body">--}}
            {{--You are logged in PhoneX License Server--}}
        {{--</div>--}}
    {{--</div>--}}

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Home
            {{--<small>Optional description</small>--}}
        </h1>

        @include('navigation.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <p>You are logged in PhoneX License Server.</p>
    </section><!-- /.content -->

@endsection
