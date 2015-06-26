@extends('app')

@section('content')

{{--<div class="container">--}}

	<section class="content-header">
		<div class="row">
			<div class="col-sm-12 pull-left">
				<h1>
					Licenses
					{{--<small>Edit</small>--}}
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
					<h2>License #{{ $license->id }} ({{ $license->user->username }})</h2>
				</div>
				<div class="right-cell">
                    <a class="btn btn-sm btn-primary view-btn-create" href="/licenses">
                        <i class="fa fa-angle-left"></i> Back to licenses
                    </a>

				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">

                    @include('errors.notifications')

                    {!! \Form::model($license, array('method' => 'patch', 'route' => array('licenses.update', $license->id))) !!}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <h4>Details</h4>

                        <div class="row">
                            <div class="col-md-6 ">
                                <dl>
                                    <dt>Issued to</dt>
                                    <dd><a href="{{ \URL::route('users.show', $license->user_id) }}">{{ $license->user->username }}</a></dd>
                                </dl>
                            </div>
                            <div class="col-md-3 ">
                                <dl>
                                    <dt>Expiration</dt>
                                    <dd>{{ $license->licenseType->readableType() }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-3 ">
                                <dl>
                                    <dt>Type</dt>
                                    <dd>{{ ucfirst($license->licenseFuncType->name) }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 ">
                                <dl>
                                    <dt>Active</dt>
                                    <dd>@if($license->isActive()) Yes @else No @endif</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 ">
                                <dl>
                                    <dt>Start date</dt>
                                    <dd>{{ date_simple($license->starts_at) }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6 ">
                                <dl>
                                    <dt>Expire date</dt>
                                    <dd>{{ date_simple($license->expires_at) }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 ">

                                <div class="form-group">
                                    <label for="password" class="control-label">Issuer (username)</label>

                                    {!! \Form::text('issuer_username', ($license->issuer ? $license->issuer->username : null) , ['class' => 'form-control']) !!}

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label for="password" class="control-label">License notes</label>
                                    {!! \Form::textarea('comment', null, ['rows'=>'8', 'class'=> 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div><input class="btn-large btn-primary btn" type="submit" value="Update"></div>
                            </div>
                        </div>
                    {!! \Form::close() !!}
				</div>
			</div>
		</div>
	</div>


	</section>

{{--</div>--}}


@endsection
