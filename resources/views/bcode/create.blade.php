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
					<h2>Generate Mobil Pohotovost code pairs</h2>
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

					<form  role="form" method="POST" action="/bcodes/generate-mp-codes">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						{{--<h4>Details</h4>--}}

						<div class="row">
							<div class="col-md-4 ">
								<div class="form-group"><label for="number_of_pairs" class="control-label">Number of pairs*</label>
									<input class="form-control" required value="{{ old('number_of_pairs') }}" placeholder="" id="number_of_pairs" type="number" name="number_of_pairs">
									<span class="help-block">Mobil Pohotovost requires pairs of business codes to be generated. Each pair is printed on a separated card and handed to customer.</span>
								</div>
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
