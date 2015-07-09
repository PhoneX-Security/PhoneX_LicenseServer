@extends('content-with-header')

@section('title', 'User: '. $user->username)
@section('subtitle', 'New license')

@section('content')
    @parent

    <section class="content">

        <div class="row">
            <div class="col-sm-12">

                @include('errors.notifications')

                <div class="box box-default">
                    <div class="box-header with-border">

                        <div class="row form-inline" style="margin-bottom: 5px">
                            <div class="col-sm-6">
                                <a class="btn btn-primary" href="{{route('users.licenses', [$user->id])}}">
                                    <i class="fa fa-close"></i> Cancel
                                </a>
                            </div>

                            <div class="col-sm-6 text-right">
                            </div>
                        </div>
                    </div>


                    <div class="box-body">

                        <form role="form" method="POST" action="{{route('users.new-license', [$user->id])}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <h4>License details</h4>

                            @include('user.chips.form-new-license')

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