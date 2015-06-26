@extends('content-with-header')

@section('title', 'User: '. $user->username)
{{--@section('subtitle', 'Manage')--}}

@section('content')
    @parent

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

            @include('user.chips.top-nav')

            @include('errors.notifications')

            <div class="box box-default">
                <div class="box-header with-border">

                    <div class="row form-inline" style="margin-bottom: 5px">
                        <div class="col-sm-6">
                            <a class="btn btn-primary">Add contact</a>
                        </div>

                        <div class="col-sm-6 text-right">
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    @include('user.chips.cl-table', ['licenses' => $user->issuedLicenses, 'show_username'=>true])
                </div>
            </div>
		</div>
	</div>

	</section>

@endsection
