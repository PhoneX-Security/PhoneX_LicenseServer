@extends('content-with-header')

@section('title', 'Text report')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">...</h3>
                <div class="box-tools">
                    <form class="form-inline inline-block"  action="/stats/text-report" method="get">

                        <div class="form-group">
                            <label for="exampleInputEmail1" style="margin: 0 5px"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i> Range </label>
                            <input  type="text" class="form-control" value="{{old('daterange', $daterange)}}" name="daterange">
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
                                        'This week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
                                        'Last week': [moment().subtract(1, 'week').startOf('isoWeek'), moment().subtract(1, 'week').endOf('isoWeek')],
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
                <h3>New users with licenses</h3>

                <ul>
                @foreach($newUsersData as $licFuncTypeId => $d)
                    @foreach($d as $licTypeId => $dd)
                        <li>{{$licenseFuncTypes[$licFuncTypeId]->uc_name . " (" . $licenseTypes[$licTypeId]->name . ")" }} <br />
                            <ul>
                                <li>Total: {{$dd->totalCount}}</li>
                                <li>Never logged in: {{$dd->neverLoggedInCount}}</li>
                                <li>Countries:
                                    @foreach($dd->countriesCount as $country => $count)
                                        {{$country . " (" . $count . "), "}}
                                    @endforeach
                                </li>
                                <li>Platforms:
                                    @foreach($dd->platformsCount as $platform => $count)
                                        {{$platform . " (" . $count . "), "}}
                                    @endforeach
                                </li>
                            </ul>
                        </li>
                    @endforeach
                @endforeach
                </ul>

                <h3>Licenses issued to existing users</h3>

                <ul>
                    @foreach($existingUsersData as $licFuncTypeId => $d)
                        @foreach($d as $licTypeId => $dd)
                            <li>{{$licenseFuncTypes[$licFuncTypeId]->uc_name . " (" . $licenseTypes[$licTypeId]->name . ")" }} <br />
                                <ul>
                                    <li>Total: {{$dd}}</li>
                                </ul>
                            </li>
                        @endforeach
                    @endforeach
                </ul>

            </div>
        </div>

    </section>
@endsection
