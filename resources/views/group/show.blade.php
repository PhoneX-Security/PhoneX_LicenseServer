@extends('content-with-header')

@section('title', 'Group: '. $group->name)

@section('content')
    @parent

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

            @include('errors.notifications')

            @include('group.chips.top-nav')

            <div class="box box-default">
                <div class="box-header with-border">
                    @yield('options')
                </div>

                <div class="box-body">
                    @yield('details')
                </div>
            </div>
		</div>
	</div>


	</section>

@endsection
