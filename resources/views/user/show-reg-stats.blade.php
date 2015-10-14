@extends('content-with-header')

@section('title', 'User: '. $user->username)

@section('js-scripts')
    <script src="/plugins/chartjs/Chart.min.js" type="text/javascript"></script>
@endsection

@section('content')
    @parent

	<section class="content">

	<div class="row">
		<div class="col-sm-12">

            @include('errors.notifications')

            {{--<div class="alert alert-danger">--}}
                {{--<strong>Warning!</strong> This is an experimental feature. Data shown below do not correspond to all user's activities, typically only to login and adding new contacts. We need to enhance this in the future.--}}
            {{--</div>--}}

            @include('user.chips.top-nav')

            <div class="box box-default">
                <div class="box-header with-border">

                    {{--<input type="text" name="daterange" value="01/01 01:30 ~ 01/01 02:00" />--}}

                    <form class="form-inline inline-block"  action="#" method="get">
                        <div class="input-group form-group">
                            <input type="text" style="width: 200px" class="form-control" value="{{old('daterange', $daterange)}}" name="daterange">
                            {{----}}
                            {{--<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>--}}
                        </div>
                        <button type="submit" class="btn btn-default">Filter</button>

                        <script type="text/javascript">
                            $(function() {
                                $('input[name="daterange"]').daterangepicker({
                                    timePicker: true,
                                    timePicker12Hour: false,
                                    timePickerIncrement: 5,
                                    format: 'MM/DD hh:mm',
                                    separator: ' ~ '
//                                    locale: {
//                                        format: 'MM/DD hh:mm',
//                                        separator: ' ~ '
//                                    }
                                });
                            });
                        </script>
                    </form>

                </div>

                <div class="box-body">

                    <div class="chart">
                        <canvas id="chart1" height="400"></canvas>
                    </div>

                    <div class="chart" >
                        <canvas id="chart2" height="150"></canvas>
                    </div>
                    <div class="chart" >
                        <canvas id="chart3" height="150"></canvas>
                    </div>

                        <script>
                            $(function () {
                                var options = {
                                    animation:false,
                                    animationEasing: "easeOutElastic",
                                    showTooltips: true,

                                    // String - Template string for single tooltips
                                    {{--tooltipTemplate: "<%if (label){%><%=label%>:  <%}%><%= value %>",--}}
                                    tooltipTemplate: "<%=datasetLabel%>: <%= value %>",

                                    // String - Template string for multiple tooltips
                                    multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>",

                                    scaleShowLabels: false,
                                    ///Boolean - Whether grid lines are shown across the chart
                                    scaleShowGridLines : true,

                                    //String - Colour of the grid lines
                                    scaleGridLineColor : "rgba(0,0,0,.05)",

                                    //Number - Width of the grid lines
                                    scaleGridLineWidth : 1,

                                    //Boolean - Whether to show horizontal lines (except X axis)
                                    scaleShowHorizontalLines: true,

                                    //Boolean - Whether to show vertical lines (except Y axis)
                                    scaleShowVerticalLines: true,

                                    //Boolean - Whether the line is curved between points
                                    bezierCurve : false,

                                    //Boolean - Whether to show a dot for each point
                                    pointDot : true,

                                    //Number - Radius of each point dot in pixels
                                    pointDotRadius : 2,

                                    //Number - Pixel width of point dot stroke
                                    pointDotStrokeWidth : 1,

                                    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                                    pointHitDetectionRadius : 5,

                                    //Boolean - Whether to show a stroke for datasets
                                    datasetStroke : false,

                                    //Number - Pixel width of dataset stroke
                                    datasetStrokeWidth : 2,
                                    //String - A legend template
                                    {{--legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",--}}
                                    //Boolean - whether to make the chart responsive
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    datasetFill: false
                                };

                                var data = {
                                    {{--labels: [{!! implode(',', $labels) !!}],--}}
                                    labels: {!! $labels1 !!},
                                    datasets: [
                                        {
                                            label: "Port",
                                            fillColor: "rgba(60,141,188,0.9)",
                                            strokeColor: "rgba(200,200,200,0.4)", // iv
                                            pointColor: "#3b8bba",
                                            pointStrokeColor: "rgba(60,141,188,1)",
                                            pointHighlightFill: "#fff",
                                            pointHighlightStroke: "rgba(60,141,188,1)",
                                            data: {!! $dataPort !!}
                                        },
                                        {
                                            label: "Cseq",
                                            fillColor: "rgba(60,141,188,0.0)",
                                            strokeColor: "rgba(200,200,200,0.4)", // iv
                                            pointColor: "#cc6",
                                            pointStrokeColor: "rgba(204,204,102,1)",
                                            pointHighlightFill: "#fff",
                                            pointHighlightStroke: "rgba(60,141,188,1)",
                                            data: {!! $dataCseq !!}
                                        },
                                        {
                                            label: "SockState",
                                            fillColor: "rgba(60,141,188,0.0)",
                                            strokeColor: "rgba(200,200,200,0.0)", // iv
                                            pointColor: "#fff",
                                            pointStrokeColor: "rgba(204,204,102,0)",
                                            pointHighlightFill: "#0fff",
                                            pointHighlightStroke: "rgba(60,141,188,0)",
                                            data: {!! $dataSockState !!}
                                        }
                                    ]
                                };
                                var data2 = {
                                    labels: {!! $labels1 !!},
                                    datasets: [
                                        {
                                            label: "SockState",
                                            fillColor: "rgba(60,141,188,0.0)",
                                            strokeColor: "rgba(255,0,0,0.1)", // iv
                                            pointColor: "#ff0000",
                                            pointStrokeColor: "rgba(204,204,102,0)",
                                            pointHighlightFill: "#0fff",
                                            pointHighlightStroke: "rgba(60,141,188,0)",
                                            data: {!! $dataSockState !!}
                                        }
                                    ]
                                };
                                var data3 = {
                                    labels: {!! $labels1 !!},
                                    datasets: [
                                        {
                                            label: "NumRegs",
                                            fillColor: "rgba(255,0,0,0.1)",
                                            strokeColor: "rgba(255,0,0,0.1)", // iv
                                            pointColor: "#ff0000",
                                            pointStrokeColor: "rgba(204,204,102,0)",
                                            pointHighlightFill: "#0fff",
                                            pointHighlightStroke: "rgba(60,141,188,0)",
                                            data: {!! $dataNumRegs !!}
                                        }
                                    ]
                                };
                                var canvas = $("#chart1").get(0).getContext("2d");
                                var canvas2 = $("#chart2").get(0).getContext("2d");
                                var canvas3 = $("#chart3").get(0).getContext("2d");
                                var chart = new Chart(canvas);
                                var chart2 = new Chart(canvas2);
                                var chart3 = new Chart(canvas3);

                                options.datasetFill = false;
                                // make a deep copy
                                var options2 = jQuery.extend(true, {}, options)
                                options2.datasetFill = true;

                                chart.Line(data, options);
                                chart2.Line(data2, options);
                                chart3.Line(data3, options2);
                            });
                        </script>



                    <br />

                    <p><b>Axis X</b> - Time  <br />
                    </p><b>Axis Y</b>  - Ports / Cseq / SockState (1=NotNull, 0=Null)

                </div>
            </div>
		</div>
	</div>

	</section>

@endsection
