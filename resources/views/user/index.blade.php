@extends('app')

@section('content')
{{--<div class="container">--}}
	{{--<div class="row">--}}
		{{--<div class="col-md-10 col-md-offset-1">--}}
			{{--<div class="panel panel-default">--}}
				{{--<div class="panel-heading">Home</div>--}}

				{{--<div class="panel-body">--}}
					{{--My Godness!--}}
				{{--</div>--}}
			{{--</div>--}}
		{{--</div>--}}
	{{--</div>--}}
{{--</div>--}}

<div class="container">
<section class="content-header">
	<div class="row">
	<h1>
		Users
		<small> Index</small>
	</h1>
	</div>

	<div class="row">

		<ol class="breadcrumb">
			<li><a href="http://demo.lavalite.org/admin"><i class="fa fa-dashboard"></i> Home </a></li>
			<li class="active">Groups</li>
		</ol>

	</div>
</section>

<section class="content">    <!-- Success-Messages -->

	<div class="row">
		<div class="col-sm-4">
			<h3 style="margin-top: 0" t>Users</h3>
		</div>
		<div class="col-sm-4 pull-right text-right center">
			<a class="btn btn-sm btn-info view-btn-create" href="#">
				<i class="fa fa-plus-circle"></i> New User
			</a>
		</div>

			{{--<div class="box-tools">--}}

				{{--<form class="form-horizontal pull-right" action="http://demo.lavalite.org/admin/user/group" method="get" style="width:50%;margin-right:5px;">--}}
					{{--<input name="_token" type="hidden" value="07R1JSMuPDKC9vGb2WY4PwrSR1tpNPXCndaRUQ4F">--}}
					{{--<div class="input-group">--}}
						{{--<input type="search" class="form-control input-sm" name="q" value=""  placeholder="Search">--}}
                    {{--<span class="input-group-btn">--}}
                        {{--<button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>--}}
                    {{--</span>--}}
					{{--</div>--}}
				{{--</form>--}}

			{{--</div>--}}
	</div><!-- /.box-header -->



	<div class="row panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-2">[Filters to add]</div>
					<div class="text-right pull-right col-xs-2">Total: 25</div>
				</div>
			</div>
			<table class="table table-condensed">
				<tr>
					<th>{!! link_to_sort('id', 'ID') !!}</th>
					<th>{!! link_to_sort('username', 'Username') !!}</th>
					<th>{!! link_to_sort('email', 'E-mail') !!}</th>
					<th>{!! link_to_sort('has_access', 'Has access') !!}</th>
					<th>Roles</th>
					<th class="text-center">Options</th>
				</tr>
				@foreach($users as $user)
					<tr>

						<td>{{ $user->id }}</td>
						<td><a href="#">{{ $user->username }}</a></td>
						<td>{{ $user->email or '' }}</td>
						<td>@if($user->has_access) Yes @else No @endif</td>

						<td>
							<i class="fa fa-check-square fa-fw"></i> Admin
							<i class="fa fa-check-square fa-fw"></i> Superuser
							<i class="fa fa-check-square fa-fw"></i> User
							<i class="fa fa-check-square fa-fw"></i> Developer
						</td>

						<td class="text-center">
							<div class="btn-group  btn-group-xs">
								<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/1/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
								<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/1" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
							</div>
						</td>
					</tr>
				@endforeach
			</table>


		</div>
		<div class="text-center">
			{!! $users->render(); !!}
		</div>
</section>

</div>


@endsection
