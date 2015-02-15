@extends('app')

@section('content')

<div class="container">

	<section class="content-header">
		<div class="row">
			<div class="col-sm-12 pull-left">
				<h1>
					Users
					{{--<small>New user</small>--}}
				</h1>
				<ol class="breadcrumb">
					<li><a href="#"><i class="fa fa-home"></i> Home </a></li>
					<li class="active">Users</li>
				</ol>
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
						<i class="fa fa-angle-left"></i> Back
					</a>
                    <a class="btn btn-sm btn-primary" href="/users">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a class="btn btn-sm btn-primary" href="/users">
                        <i class="fa fa-edit"></i> Issue license
                    </a>

				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">

						{{--<h4>Details</h4>--}}



                    <div class="" role="tabpanel" >
                        <div class="row" style="margin-bottom: 15px">
                            <div class="col-md-12">
                                <ul id="myTab" class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#home" id="home-tab" role="tab" data-toggle="tab">Details</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#profile" role="tab" id="profile-tab" data-toggle="tab">Licenses</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content" style="padding-left: 10px; padding-right: 15px">
                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <dl>
                                            <dt>Username</dt>
                                            <dd>{{ $user->username }}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6 ">
                                        <dl>
                                            <dt>Email Address</dt>
                                            <dd>{{ $user->email or '-'}}</dd>
                                        </dl>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 ">
                                        <dl>
                                            <dt>Has access</dt>
                                            <dd>@if ($user->has_access) Yes @else No @endif</dd>
                                        </dl>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 ">
                                        </dl>
                                            <dt>Date created</dt>
                                            <dd>{{ date_simple($user->created_at) }}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6 ">
                                        <dl>
                                            <dt>Date updated</dt>
                                            <dd>{{ date_simple($user->updated_at) }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="profile">

                                @if($user->licenses && count($user->licenses) > 0)
                                <table class="table table-condensed">
                                    <tr>
                                        <th>License type</th>
                                        <th>Trial</th>
                                        <th>Active</th>
                                        <th>Start date</th>
                                        <th>Expiration date</th>
                                        <th width="300">Comment</th>

                                        <th>Options</th>
                                    </tr>
                                    @foreach($user->licenses as $license)
                                        <tr>
                                            <td>{{ ucfirst($license->licenseType->name) }} ({{ $license->licenseType->days }} days)</td>
                                            <td>@if($license->licenseType->is_trial) Yes @else No @endif</td>
                                            <td>@if($license->active) Yes @else No @endif</td>
                                            <td>{{ date_simple($license->starts_at) }}</td>
                                            <td>{{ date_simple($license->expires_at) }}</td>
                                            <td>TODO</td>
                                            <td>TODO</td>
                                        </tr>
                                    @endforeach
                                </table>
                                @else
                                No licenses
                                @endif
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>


	</section>

</div>


@endsection
