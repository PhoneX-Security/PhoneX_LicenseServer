@extends('app')

@section('content')
<div class="container">
<section class="content-header">
	<div class="row">
	<h1>
		Licenses
		<small> Index</small>
	</h1>
	</div>

	<div class="row">

		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home </a></li>
			<li class="active">Licenses</li>
		</ol>

	</div>
</section>

<section class="content">    <!-- Success-Messages -->

	<div class="row">
		<div class="col-sm-4">
			<h3 style="margin-top: 0">Licenses</h3>
		</div>

		{{--<div class="col-sm-4 pull-right text-right center">--}}
			{{--<a class="btn btn-sm btn-info view-btn-create" href="#">--}}
				{{--<i class="fa fa-plus-circle"></i> New User--}}
			{{--</a>--}}
		{{--</div>--}}

	</div><!-- /.box-header -->



	<div class="row panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-8 pull-left">
						<form method="get" class="form-inline">
							@if(\Request::has('o')) <input type="hidden" name="o" value="{{ InputGet::get('o') }}" /> @endif
							@if(\Request::has('s')) <input type="hidden" name="s" value="{{ InputGet::get('s') }}" /> @endif
							<div class="form-group">
								<label class="col-md-1 control-label text-le">Filters:</label>
							</div>
							<div class="form-group">
								<label class="checkbox-inline" for="checkboxes-0">
									{!! Form::checkbox('active_only', '1', \Request::has('active_only'), ['id'=>'checkboxes-0']); !!}
									Active only
								</label>

								<label class="checkbox-inline" for="checkboxes-1">
									{!! Form::checkbox('trial_only', '1', \Request::has('trial_only'), ['id'=>'checkboxes-1']); !!}
									Trial only
								</label>

							</div>
							<button type="submit" style="margin-left: 10px" class="btn btn-default">Submit</button>
						</form>
					</div>
					<div class="text-right pull-right col-xs-2">
						<span style="line-height: 38px">Total: {{ $licenses->total() }}</span>
					</div>
				</div>
			</div>
			<table class="table table-condensed">

				<tr>
					<th >{!! link_to_sort('username', 'Username') !!}</th>
					<th>{!! link_to_sort('license_type', 'License type') !!}</th>
					<th>{!! link_to_sort('is_trial', 'Trial') !!}</th>
					<th>Active</th>
					<th>{!! link_to_sort('starts_at', 'Start date') !!}</th>
					<th>{!! link_to_sort('expires_at', 'Expiration date') !!}</th>
					<th>Options</th>
				</tr>
				@foreach($licenses as $lic)
					<tr>
						<td><a href="#">{{ $lic->username }}</a></td>
						<td>{{ $lic->license_type }}</td>
						<td>@if($lic->is_trial) Yes @else No @endif</td>
						<td>@if($lic->active) Yes @else No @endif</td>
						<td>{{ $lic->starts_at }}</td>
						<td>{{ $lic->expires_at }}</td>
						<td>TODO</td>
					</tr>
				@endforeach
				{{--@foreach($users as $user)--}}
					{{--<tr>--}}
						{{--<td><a href="#">{{ $user->username }}</a></td>--}}
						{{--<td>{{ $user->email or '' }}</td>--}}
						{{--<td>@if($user->has_access) Yes @else No @endif</td>--}}

						{{--<td class="text-center">--}}
							{{--<div class="btn-group  btn-group-xs">--}}
								{{--<a type="button" class="btn btn-info   view-btn-edit" href="#" title="Edit"><i class="fa fa-pencil-square-o"></i></a>--}}
								{{--<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="#" title="Delete user"><i class="fa fa-times-circle-o"></i></a>--}}
							{{--</div>--}}
						{{--</td>--}}
					{{--</tr>--}}
				{{--@endforeach--}}
			</table>


		</div>
		<div class="text-center">
			{!! $licenses->appends(Request::except('page'))->render(); !!}
		</div>
</section>

</div>


@endsection
