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
<div class="container-fluid">

<section class="content-header">
	<h1>
		Group
		<small> Manage Groups</small>
	</h1>

	<ol class="breadcrumb">
		<li><a href="http://demo.lavalite.org/admin"><i class="fa fa-dashboard"></i> Home </a></li>
		<li class="active">Groups</li>
	</ol>
</section>

<section class="content">    <!-- Success-Messages -->
	<div class="box  box-info">
		<div class="box-header">
			<h3 class="box-title">            Groups
			</h3>
			<div class="box-tools">
				<a class="btn   btn-sm btn-info pull-right  view-btn-create" href="http://demo.lavalite.org/admin/user/group/create">
					<i class="fa fa-plus-circle"></i> New Group
				</a>


				<form class="form-horizontal pull-right" action="http://demo.lavalite.org/admin/user/group" method="get" style="width:50%;margin-right:5px;">
					<input name="_token" type="hidden" value="07R1JSMuPDKC9vGb2WY4PwrSR1tpNPXCndaRUQ4F">
					<div class="input-group">
						<input type="search" class="form-control input-sm" name="q" value=""  placeholder="Search">
                    <span class="input-group-btn">
                        <button class="btn  btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </span>
					</div>
				</form>

			</div>
		</div><!-- /.box-header -->
		<div class="box-body table-responsive no-padding">
			<table class="table table-condensed">
				<tr>
					<th>Group</th>
					<th>Permissions</th>
					<th width="70">Options</th>
				</tr>
				<tr>
					<td><a href="http://demo.lavalite.org/admin/user/group/1">Superuser</a></td>
					<td>
						<i class="fa fa-check-square fa-fw"></i> Admin
						<i class="fa fa-check-square fa-fw"></i> Superuser
						<i class="fa fa-check-square fa-fw"></i> User
						<i class="fa fa-check-square fa-fw"></i> Developer
					</td>
					<td>
						<div class="btn-group  btn-group-xs">
							<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/1/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
							<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/1" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
						</div>
					</td>
				</tr>
				<tr>
					<td><a href="http://demo.lavalite.org/admin/user/group/2">Admin</a></td>
					<td>
						<i class="fa fa-check-square fa-fw"></i> Admin
						<i class="fa fa-check-square fa-fw"></i> Superuser
						<i class="fa fa-times fa-fw"></i> User
						<i class="fa fa-times fa-fw"></i> Developer
					</td>
					<td>
						<div class="btn-group  btn-group-xs">
							<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/2/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
							<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/2" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
						</div>
					</td>
				</tr>
				<tr>
					<td><a href="http://demo.lavalite.org/admin/user/group/3">User</a></td>
					<td>
						<i class="fa fa-times fa-fw"></i> Admin
						<i class="fa fa-times fa-fw"></i> Superuser
						<i class="fa fa-check-square fa-fw"></i> User
						<i class="fa fa-times fa-fw"></i> Developer
					</td>
					<td>
						<div class="btn-group  btn-group-xs">
							<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/3/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
							<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/3" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
						</div>
					</td>
				</tr>
				<tr>
					<td><a href="http://demo.lavalite.org/admin/user/group/4">test</a></td>
					<td>
						<i class="fa fa-check-square fa-fw"></i> Admin
						<i class="fa fa-times fa-fw"></i> Superuser
						<i class="fa fa-times fa-fw"></i> User
						<i class="fa fa-times fa-fw"></i> Developer
					</td>
					<td>
						<div class="btn-group  btn-group-xs">
							<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/4/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
							<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/4" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
						</div>
					</td>
				</tr>
				<tr>
					<td><a href="http://demo.lavalite.org/admin/user/group/5">cool</a></td>
					<td>
						<i class="fa fa-check-square fa-fw"></i> Admin
						<i class="fa fa-check-square fa-fw"></i> Superuser
						<i class="fa fa-check-square fa-fw"></i> User
						<i class="fa fa-check-square fa-fw"></i> Developer
					</td>
					<td>
						<div class="btn-group  btn-group-xs">
							<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/5/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
							<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/5" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
						</div>
					</td>
				</tr>
				<tr>
					<td><a href="http://demo.lavalite.org/admin/user/group/6">sdfsdfsfsfsdf</a></td>
					<td>
						<i class="fa fa-check-square fa-fw"></i> Admin
						<i class="fa fa-times fa-fw"></i> Superuser
						<i class="fa fa-times fa-fw"></i> User
						<i class="fa fa-times fa-fw"></i> Developer
					</td>
					<td>
						<div class="btn-group  btn-group-xs">
							<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/6/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
							<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/6" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
						</div>
					</td>
				</tr>
				<tr>
					<td><a href="http://demo.lavalite.org/admin/user/group/7">testtttt</a></td>
					<td>
						<i class="fa fa-check-square fa-fw"></i> Admin
						<i class="fa fa-check-square fa-fw"></i> Superuser
						<i class="fa fa-check-square fa-fw"></i> User
						<i class="fa fa-times fa-fw"></i> Developer
					</td>
					<td>
						<div class="btn-group  btn-group-xs">
							<a type="button" class="btn btn-info   view-btn-edit" href="http://demo.lavalite.org/admin/user/group/7/edit" title="Update group"><i class="fa fa-pencil-square-o"></i></a>
							<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="http://demo.lavalite.org/admin/user/group/7" title="Delete group"><i class="fa fa-times-circle-o"></i></a>
						</div>
					</td>
				</tr>
			</table>
		</div><!-- /.box-body -->
	</div>
</section>

</div>


@endsection
