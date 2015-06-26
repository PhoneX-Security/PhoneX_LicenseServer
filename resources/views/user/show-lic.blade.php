@extends('content-with-header')

@section('title', 'User: '. $user->username)

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
                            <a class="btn btn-primary">Issue license</a>
                        </div>

                        <div class="col-sm-6 text-right">
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <h4>Licenses</h4>
                    @include('license.chips.licenses_table', ['licenses' => $user->licenses, 'show_issuer'=>true])

                    <h4>Licenses issued by user</h4>
                    @include('license.chips.licenses_table', ['licenses' => $user->issuedLicenses, 'show_username'=>true])
                </div>
            </div>

		</div>
	</div>
	</section>

@endsection
