@extends('form.form-create')
@section('title', "New code pairs export")
@section('form')
    <form  role="form" method="POST" action="/bcodes/generate-code-pairs">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <h4>Code pair</h4>
        <p>Specify number of pairs you want to generate and license details. After both codes are used, newly created users are automatically connected via their contact lists.</p>

        <div class="row">
            <div class="col-md-2 ">
                <div class="form-group"><label for="number_of_pairs" class="control-label">Number of pairs*</label>
                    <input class="form-control" required value="{{ old('number_of_pairs') }}" placeholder="" id="number_of_pairs" type="number" name="number_of_pairs">
                    {{--<span class="help-block"></span>--}}
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="expires_at" class="control-label">Expiration date (optional)</label>
                    <div class="input-group date">
                        <input value="{{ old('expires_at') }}" type="text" name="expires_at" class="form-control" />
                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(function () {
                    $('#expires_at').daterangepicker();
                });
            </script>

            <div class="col-md-2 ">
                <div class="form-group">
                    <label for="email" class="control-label">Email to export codes*</label>
                    <input class="form-control" required value="{{ old('email') }}" placeholder="" id="email" type="email" name="email">
                    <span class="help-block">Generated codes will be exported to provided email address.</span>
                </div>
            </div>
        </div>

        <h4>License</h4>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="password" class="control-label">Expiration</label>
                            <select name="license_type_id" class="form-control" >
                                @foreach($licenseTypes as $type)
                                    <option @if($type->default) selected="selected" @endif
                                    value="{{ $type->id }}">{{ ucfirst($type->name) . " (" . $type->days . " days)" }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="license_func_type_id" class="control-label">Type</label>
                        <select id="license_func_type_id" name="license_func_type_id" class="form-control">
                            @foreach($licenseFuncTypes as $type)
                                <option value="{{ $type->id }}" @if($type->default) selected="selected" @endif>{{ ucfirst($type->name) }} </option>
                            @endforeach
                        </select>
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
                            {{--<span class="help-block">User will be added to this group. Its owner will be added as a support account.</span>--}}
                        </div>
                    </div>

                    <div class="col-md-2 ">
                        <div class="form-group">
                            <label for="parent_username" class="control-label">Parent (username)</label>
                            <input class="form-control" value="{{ old('parent_username') }}"
                                   placeholder="Parent username" id="parent_username" type="text" name="parent_username">
                            {{--<span class="help-block">Parent user will be added as a support account. (This has bigger priority than group's owner)</span>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 ">
                <div class="form-group">
                    <label for="comment" class="control-label">Comment</label>
                    <textarea id="comment" name="comment" class="form-control" rows="3"></textarea>
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
@endsection