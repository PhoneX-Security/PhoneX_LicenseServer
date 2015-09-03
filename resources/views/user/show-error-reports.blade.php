@extends('content-with-header')

@section('title', 'User: '. $user->username)

@section('content')
    @parent

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

            @include('errors.notifications')

            @include('user.chips.top-nav')

            <div class="box box-default">
                <div class="box-header with-border">

                    <div class="row form-inline" style="margin-bottom: 5px">
                        <div class="col-sm-6">

                        </div>

                        <div class="col-sm-6 text-right">
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    @include('user.chips.errors-table', ['reports' => $reports, 'with_username'=>false])
                </div>
            </div>
		</div>
	</div>
	</section>
@endsection
