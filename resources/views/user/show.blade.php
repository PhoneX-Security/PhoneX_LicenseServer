@extends('app')

@section('content')

{{--<div class="container">--}}

	<section class="content-header">
		<div class="row">
			<div class="col-sm-12 pull-left">
				<h1>
					Users
					{{--<small>New user</small>--}}
				</h1>
                @include('navigation.breadcrumb')
			</div>
		</div>
	</section>

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

			<div class="phonex-table-div clearfix">
				<div class="left-cell">
					<h2>User {{ $user->username }}</h2>
				</div>
				<div class="right-cell">

					<a class="btn btn-sm btn-primary view-btn-create" href="/users">
						<i class="fa fa-angle-left"></i> Back to users
					</a>
                    <a class="btn btn-sm btn-primary" href="{{ \URL::route('users.edit', [$user->id]) }}">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    {{--<a class="btn btn-sm btn-primary" href="/users">--}}
                        {{--<i class="fa fa-edit"></i> Issue license--}}
                    {{--</a>--}}

				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">

                    @include('errors.notifications')

					{{--<h4>Details</h4>--}}

                    <div class="" role="tabpanel" >
                        <div class="row" style="margin-bottom: 15px">
                            <div class="col-md-12">
                                <ul id="myTab" class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#home-t" id="home-tab" role="tab" data-toggle="tab">Details</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#licenses-t" role="tab" id="profile-tab" data-toggle="tab">Licenses</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#issued-licenses-t" role="tab" id="issued-licenses-tab" data-toggle="tab">Issued licenses</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#contact-list-t" role="tab" id="contact-list-tab" data-toggle="tab">Contact list</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content" style="padding-left: 10px; padding-right: 15px">
                            <div role="tabpanel" class="tab-pane fade in active" id="home-t">
                                @include('user.chips.user_details', ['user' => $user])
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="licenses-t">
                                @include('license.chips.licenses_table', ['licenses' => $user->licenses, 'show_issuer'=>true])
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="issued-licenses-t">
                                @include('license.chips.licenses_table', ['licenses' => $user->issuedLicenses, 'show_username'=>true])
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="contact-list-t">
                                @include('user.chips.contact_list_table', ['licenses' => $user->issuedLicenses, 'show_username'=>true])
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>


	</section>

{{--</div>--}}


@endsection
