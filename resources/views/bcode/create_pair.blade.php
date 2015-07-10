@extends('form.form-create')
@section('title', "New business code pairs")
@section('form')
    <form  role="form" method="POST" action="/bcodes/generate-code-pairs">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        {{--<h4>Details</h4>--}}

        <div class="row">
            <div class="col-md-2 ">
                <div class="form-group"><label for="number_of_pairs" class="control-label">Number of pairs*</label>
                    <input class="form-control" required value="{{ old('number_of_pairs') }}" placeholder="" id="number_of_pairs" type="number" name="number_of_pairs">
                    <span class="help-block">Specify number of pairs you want to generate. After both codes are used, their users are automatically connected in contact list.</span>
                </div>
            </div>

            <div class="col-md-3 ">
                <div class="form-group">
                    <label for="email" class="control-label">Email to export codes*</label>
                    <input class="form-control" required value="{{ old('email') }}" placeholder="" id="email" type="email" name="email">
                    <span class="help-block">Generated codes will be exported to provided email address.</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="password" class="control-label">Expiration type</label>
                    <select name="license_type_id" class="form-control" >
                        @foreach($licenseTypes as $type)
                            <option @if($type->default) selected="selected" @endif
                            value="{{ $type->id }}">{{ ucfirst($type->name) . " (" . $type->days . " days)" }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="" class="control-label">Type</label>
                    <input class="form-control" disabled value="Full" type="text" >
                </div>
            </div>

            <div class="col-md-2 ">
                <div class="form-group">
                    <label for="parent_username" class="control-label">Parent (username)</label>
                    <input class="form-control" value="{{ old('parent_username') }}"
                           placeholder="Parent username" id="parent_username" type="text" name="parent_username">
                    <span class="help-block">Parent user will be added as a support account. If empty, <b>phonex-support</b> is added as support account</span>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="group_id" class="control-label">Group</label>
                    <select name="group_id" class="form-control">
                        <option selected disabled>-Select group-</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }} </option>
                        @endforeach
                    </select>
                    <span class="help-block">User will be added to this group.</span>
                </div>
            </div>
        </div>

        <div class="row">

        </div>

        <p>* Required fields</p>

        <div class="row">
            <div class="col-md-12">
                <div><input class="btn-large btn-primary btn" type="submit" value="Submit"> <input class="btn-large btn-default btn" type="reset" value="Reset"></div>
            </div>
        </div>
    </form>
@endsection