@extends('content-with-header')

@section('title', 'Homepage')
{{--@section('subtitle', 'Index')--}}

@section('content')
    @parent

    <section class="content">
        <p>Welcome to PhoneX License Server.</p>
    </section><!-- /.content -->
@endsection
