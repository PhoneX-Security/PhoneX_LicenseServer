@extends('content-with-header')

@section('title', 'Error reports')

@section('content')
    @parent

    <section class="content">

        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Users</h3>
            </div><!-- /.box-header -->

            <div class="box-body">

                @include('user.chips.errors-table', ['reports' => $reports, 'with_username'=>true])

            </div>
        </div><!-- /.box -->
    </section>
@endsection