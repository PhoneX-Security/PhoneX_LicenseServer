@extends('content-with-header')
{{-- Define title section for child view --}}

@section('content')
    @parent

    <section class="content">

        <div class="row">
            <div class="col-sm-12">

                @include('errors.notifications')

                <div class="box box-default">
                    <div class="box-header with-border">
                        @yield('form-nav')
                    </div>

                    <div class="box-body">
                        @yield('form')
                    </div>
                </div>
            </div>
        </div>

    </section>

@endsection