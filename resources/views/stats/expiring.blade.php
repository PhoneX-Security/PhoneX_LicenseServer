@extends('content-with-header')

@section('title', 'Users with expiring licenses')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">..</h3>
                <div class="box-tools">
                    <form class="form-inline inline-block"  action="/stats/expiring" method="get">

                        <div class="form-group">
                            <label for="lic_types" style="margin: 0 5px">Expiration types</label>
                            <select id="lic_types" name="lic_type_ids[]" class="multiselect-basic"  multiple="multiple">
                                @foreach($licTypes as $licType)
                                    <option value="{{ $licType->id }}" @if($licType->selected) selected="selected" @endif>{{ $licType->uc_name_with_days }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="lic_func_types" style="margin: 0 5px">License types</label>
                            <select id="lic_func_types" name="lic_func_type_ids[]" class="multiselect-basic"  multiple="multiple">
                                @foreach($licFuncTypes as $licFuncType)
                                    <option value="{{ $licFuncType->id }}" @if($licFuncType->selected) selected="selected" @endif>{{ $licFuncType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group form-group">
                            <input  type="text" class="form-control" value="{{old('daterange', $daterange)}}" name="daterange">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>

                        <script type="text/javascript">
                            $(function() {
                                function cb(start, end) {
                                    console.log(start.format('YYYY-MM-DD'));
                                    console.log(end.format('YYYY-MM-DD'));
                                }
                                cb(moment().subtract(29, 'days'), moment());
                                $('input[name="daterange"]').daterangepicker({
                                    locale: {
                                        "firstDay": 1
                                    },
                                    ranges: {
                                        'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                                        'Next 7 Days': [moment(), moment().add(6, 'days')],
                                        'In two weeks': [moment().add(7, 'days'), moment().add(13, 'days')],
                                    }
                                }, cb);
                            });
                        </script>


                        <button type="submit" class="btn btn-default">Filter</button>
                    </form>

                </div>
            </div>
            <div class="box-body">
                @include('user.chips.users-simple-table', ['users' => $users])
            </div>
        </div>

    </section>
@endsection
