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
                </div>

                <div class="box-body">

                    <div class="chart">
                        <canvas id="chart1" height="250"></canvas>

                        <script>
                            $(function () {
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
                                var canvas = $("#chart1").get(0).getContext("2d");
                                var chart = new Chart(canvas);

                                var options = {
                                    animation:false,
                                    animationEasing: "easeOutElastic",
                                    showTooltips: true,

                                    // String - Template string for single tooltips
                                    tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",

                                    // String - Template string for multiple tooltips
                                    multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>",

                                    scaleShowLabels: true,
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
                                    maintainAspectRatio: false
                                };

                                options.datasetFill = false;
                                chart.Line(data, options);
                            });
                        </script>

                    </div>

                    <br />

                    <p><b>Axis X</b> - Time  <br />
                    </p><b>Axis Y</b>  - Ports / Cseq / SockState (1=NotNull, 0=Null)

                </div>
            </div>
		</div>
	</div>

	</section>

@endsection
