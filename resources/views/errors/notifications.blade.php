@if(Session::has('success'))
    <div class="alert alert-success alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Success!</strong> {{ Session::get('success') }}
    </div>
@endif

@if (count($errors) > 0)
	<div class="alert alert-danger">
		<strong>No!</strong> {{ $error_title or 'There were some problems with your input.' }} <br><br>
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif