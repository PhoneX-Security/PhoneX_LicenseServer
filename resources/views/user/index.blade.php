@extends('app')

@section('content')

<div class="container">
	<section class="content-header">
		<div class="row">
			<div class="col-sm-12">
				<h1>
					Users
					<small>Manage users</small>
				</h1>
                @include('navigation.breadcrumb')
			</div>
		</div>
	</section>

	<section class="content">

    @include('errors.notifications')

	<div class="row">
		<div class="col-sm-12">
			<div style="margin-bottom: 10px">
				<form class="form-inline">
					<a class="btn btn-sm btn-primary view-btn-create" href="/users/create">
						<i class="fa fa-plus-circle"></i> New User
					</a>

					<form class="form-horizontal" style="width: 10%" action="#" method="get">

						<div class="input-group">
							<input type="search" class="form-control input-sm" name="q" value="" placeholder="Search">
                    <span class="input-group-btn">
                        <button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </span>
						</div>
					</form>
				</form>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-2">[Filters to add]</div>
						<div class="text-right pull-right col-xs-2">Total: {{ $users->total() }}</div>
					</div>
				</div>
				<table class="table table-condensed phonex-table-sortable">
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
							<td>
                                <a href="{{ \URL::route('users.show', [ $user->id ]) }}">{{ $user->username }}</a>
                            </td>
							<td>{{ $user->email or '' }}</td>
							<td>@if($user->has_access) Yes @else No @endif</td>

							<td>
								@if($user->has_access) <i class="fa fa-check-square fa-fw"></i> Admin @endif

								{{--<i class="fa fa-check-square fa-fw"></i> Superuser--}}
							</td>

							<td class="text-center">
								<div class="btn-group  btn-group-xs">
									<a type="button" class="btn btn-info view-btn-edit" href="{{ \URL::route('users.edit', $user->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>
									{{--<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="#" title="Delete user"><i class="fa fa-times-circle-o"></i></a>--}}
								</div>
							</td>
						</tr>
					@endforeach
				</table>
			</div>

			<div class="text-center">
				{!! $users->appends(Request::except('page'))->render(); !!}
			</div>
		</div>
	</div>


	</section>

</div>


@endsection
