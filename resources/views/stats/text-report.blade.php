@extends('content-with-header')

@section('title', 'Text report')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <form class="form-inline inline-block"  action="/stats/text-report" method="get">



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
                                        'This week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
                                        'Last week': [moment().subtract(1, 'week').startOf('isoWeek'), moment().subtract(1, 'week').endOf('isoWeek')],
                                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                    }
                                }, cb);
                            });
                        </script>

                        <div class="checkbox" style="margin: 0px 10px">
                            <label>
                                <input name="with-users" type="checkbox" @if(Request::has('with-users')) checked @endif> With users
                            </label>
                        </div>

                        <div class="checkbox" style="margin: 0px 10px">
                            <label>
                                <input name="relevant-only" type="checkbox" @if(Request::has('relevant-only')) checked @endif> For investors
                            </label>
                        </div>


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
                                <li>Total: {{$dd['count']}}</li>
                                <li>Never logged in: {{$dd['neverLoggedIn']['count']}}
                                    @if(isset($dd['neverLoggedIn']['users']) && count($dd['neverLoggedIn']['users']))
                                        @include('stats.chips.with-users', ['users' => $dd['neverLoggedIn']['users'], 'withUsers' => $withUsers])
                                    @endif
                                </li>

                                @if($dd['countries'])
                                <li>Countries:
                                    @foreach($dd['countries'] as $country => $data)
                                        {{$country . " (" . $data['count'] . ")"}}
                                        @include('stats.chips.with-users', ['users' => $data['users'], 'withUsers' => $withUsers])
                                        @if(!$withUsers && $data !== end($dd['countries'])), @endif
                                    @endforeach
                                </li>
                                @endif

                                @if($dd['platforms'])
                                    <li>Platforms:
                                        @foreach($dd['platforms'] as $platform => $data)
                                            {{$platform . " (" . $data['count'] . ")"}}
                                            @include('stats.chips.with-users', ['users' => $data['users'], 'withUsers' => $withUsers])
                                            @if(!$withUsers && $data !== end($dd['platforms'])), @endif
                                        @endforeach
                                    </li>
                                @endif

                                @if($dd['groups'])
                                    <li><b>OZ Groups:</b>
                                        @foreach($dd['groups'] as $key => $data)
                                            {{$key . " (" . $data['count'] . ")"}}
                                            @include('stats.chips.with-users', ['users' => $data['users'], 'withUsers' => $withUsers])
                                            @if(!$withUsers && $data !== end($dd['groups'])), @endif
                                        @endforeach
                                    </li>
                                @endif
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
                                    <li>Total: {{$dd['count']}}
                                        @include('stats.chips.with-users', ['users' => $dd['users'], 'withUsers' => $withUsers])
                                    </li>
                                </ul>
                            </li>
                        @endforeach
                    @endforeach
                </ul>

            </div>


        </div>

        <script>
            $(function() {
                // Expansion function
                $(".link-expand").click(function(){
//                    console.log($(this));
                    if ($(this).text() == "+"){
                        $(this).text("-")
                    } else {
                        $(this).text("+");
                    }
                    $(this).next(".expandable").toggle();
                });
            });
        </script>

    </section>
@endsection
