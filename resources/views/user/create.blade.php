@extends('content-with-header')

@section('title',"Create user")
{{--@section('subtitle',"Create")--}}

@section('content')
    @parent

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

            @include('errors.notifications')

            <div class="box box-default">
                <div class="box-header with-border">

                    {{--<div class="row form-inline" style="margin-bottom: 5px">--}}
                        {{--<div class="col-sm-6">--}}
                            {{--<a class="btn btn-primary" href="{{ \URL::route('users.show', [$user->id]) }}">--}}
                                {{--<i class="fa fa-close"></i> Cancel editing--}}
                            {{--</a>--}}
                            {{--<a class="btn btn-default" href="#"--}}
                               {{--data-href="#" data-toggle="modal" data-target="#reset-password">Reset password</a>--}}
                        {{--</div>--}}

                        {{--<div class="col-sm-6 text-right">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>



                <div class="box-body">
                    <form  role="form" method="POST" action="/users">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <h4>User details</h4>

                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="form-group"><label for="username" class="control-label">Username*</label>
                                    <input class="form-control" required value="{{ old('username') }}" placeholder="Enter username" id="username" type="text" name="username">
                                    <span class="help-block">Username serves as an login into PhoneX application.</span>
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group"><label for="password" class="control-label">Default password*</label>
                                    <input class="form-control" value="{{ old('password','phonexxx') }}" id="password" type="text" name="password">
                                    <span class="help-block">Password will be changed on first login.</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="form-group"><label class="control-label">Groups (TODO)</label>
                                    <select multiple name="groups" class="form-control">
                                        @foreach($groups as $group)
                                            <option value="{{$group->id}}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group"><label class="control-label">Roles (TODO)</label>
                                    <select multiple name="roles" class="form-control">
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="password" class="control-label">User comment</label>
                                    <textarea name="comment" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <h4>License details</h4>
                        <div class="panel panel-default">
                            <div class="panel-body">

                                @include('user.chips.form-new-license')

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
