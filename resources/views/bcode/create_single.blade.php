@extends('app')

@section('content')

	<section class="content-header">
		<div class="row">
			<div class="col-sm-12 pull-left">
				<h1>
					Business codes
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
					<h2>New single business codes</h2>
				</div>
				<div class="right-cell">

					<a class="btn btn-sm btn-primary view-btn-create" href="/bcodes">
						<i class="fa fa-angle-left"></i> Back
					</a>

				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					@include('errors.notifications')

					<form  role="form" method="POST" action="/bcodes/generate-single-codes">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						{{--<h4>Details</h4>--}}

						<div class="row">
							<div class="col-md-4 ">
								<div class="form-group"><label for="number" class="control-label">Number</label>
									<input class="form-control" required value="{{ old('number') }}" placeholder="" id="number" type="number" name="number">
								</div>
							</div>
                            <div class="col-md-3">
                                <label for="license_type_id" class="control-label">Expiration</label>
                                <select name="license_type_id" class="form-control">
                                    @foreach($licenseTypes as $type)
                                        <option value="{{ $type->id }}">{{ ucfirst($type->name) . " (" . $type->days . " days)" }} </option>
                                    @endforeach
                                </select>
                            </div>
						</div>

                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="form-group"><label for="email" class="control-label">Email to export codes*</label>
                                    <input class="form-control" required value="{{ old('email') }}" placeholder="" id="email" type="email" name="email">
                                    <span class="help-block">Generated codes will be exported to provided email address.</span>
                                </div>
                            </div>
                        </div>

						<p>* Required fields</p>

                        <div class="row">
                            <div class="col-md-12">
                                <div><input class="btn-large btn-primary btn" type="submit" value="Submit"> <input class="btn-large btn-default btn" type="reset" value="Reset"></div>
                            </div>
                        </div>
					</form>
				</div>
			</div>
		</div>
	</div>


	</section>

@endsection
