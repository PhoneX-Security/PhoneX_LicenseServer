@extends('main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            @yield('title')
            {{--hack to check if section is defined--}}
            @if (trim($__env->yieldContent('subtitle')))
                <small>@yield('subtitle')</small>
            @endif
        </h1>

        @include('navigation.breadcrumb')
    </section>

    {{--<!-- Main content -->--}}
    {{--<section class="content">--}}
        {{--@yield('content-inner')--}}
    {{--</section><!-- /.content -->--}}
@endsection
