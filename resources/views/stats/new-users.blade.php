@extends('content-with-header')

@section('title', 'New users')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Users</h3>
                <div class="box-tools">
                    <form class="form-inline inline-block"  action="/stats/new-users" method="get">

                        <div class="form-group">
                            <label for="lic_func_types" style="margin: 0 5px">License types</label>
                            <select id="lic_func_types" name="lic_func_type_ids[]" class="multiselect-basic"  multiple="multiple">
                                @foreach($licFuncTypes as $licFuncType)
                                    <option value="{{ $licFuncType->id }}" @if($licFuncType->selected) selected="selected" @endif>{{ $licFuncType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1" style="margin: 0 5px"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i> Range </label>
                            <input style="width: 170px" type="text" class="form-control" value="{{old('daterange', $daterange)}}" name="daterange">
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
                                        'Today': [moment(), moment()],
                                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
