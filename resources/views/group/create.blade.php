@extends('content-with-header')

@section('title',"Create group")

@section('content')
    @parent

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

            @include('errors.notifications')

            <div class="box box-default">
                <div class="box-header with-border">
                </div>

                <div class="box-body">
                    <form  role="form" method="POST" action="{{ route('groups.store') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <h4>Group details</h4>

                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="form-group"><label for="name" class="control-label">Name*</label>
                                    <input class="form-control" required value="{{ old('name') }}" placeholder="Enter group name" id="name" type="text" name="name">
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group"><label for="owner_username" class="control-label">Owner</label>
                                    <input class="form-control" value="{{ old('owner_username') }}" placeholder="Enter owner's username" id="owner_username" type="text" name="owner_username">
                                    {{--<span class="help-block">Owner of the group will be assigned as a support contact for newly created licenses (if not chosen otherwise during user creation).</span>--}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="comment" class="control-label">Comment</label>
                                    <textarea name="comment" class="form-control" rows="3">{{ old('comment') }}</textarea>
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
