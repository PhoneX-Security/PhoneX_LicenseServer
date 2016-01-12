<!DOCTYPE html>
<meta charset="utf-8">
<style>
    html, body {
        margin: 0;
        height: 100%;
    }

    .link {
        fill: none;
        stroke: #000;
        stroke-width: 1.5px;
    }

    circle {
        fill: #ccc;
        stroke: #000;
        stroke-width: 1.5px;
    }

    /*.node {*/
        /*cursor: pointer;*/
        /*stroke: #3182bd;*/
        /*stroke-width: 1.5px;*/
    /*}*/

    .node text {
        pointer-events: none;
        font: 14px sans-serif;
    }

    .node.fixed {
        fill: #f00;
    }

</style>
<body>
<script src="https://d3js.org/d3.v3.min.js"></script>
{{--<script src="http://d3js.org/d3.v3.js"></script>--}}
<script>
    // Example data
//    var graphData = {
//        "nodes": [
//            {"group": 1},
//            {"group": 2},
//            {"group": 3},
//            {"group": 1},
//            {"group": 1}
//        ],
//        "links": [
//            {"source":  0, "target":  1},
//            {"source":  2, "target":  0},
//            {"source":  3, "target":  0},
//            {"source":  4, "target":  0}
//        ]
//    };
    /* Functions */
    function tick() {
        link.attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

        node.attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return d.y; });
    }

    function dblclick(d) {
        d3.select(this).classed("fixed", d.fixed = false);
    }

    function dragstart(d) {
        d3.select(this).classed("fixed", d.fixed = true);
    }

    /* Data */
    var graphData = {!! $graphData !!};

    /* Graph settings*/
    var width = 1900, height = 800;
    var color = d3.scale.category10();

    var svg = d3.select("body").append("svg")
            .attr("width", "100%")
            .attr("height", "100%");
    var force = d3.layout.force()
            .size([width, height])
            .charge(-600)
            .nodes(graphData.nodes)
            .links(graphData.links)
            .linkDistance(80)
            .on("tick", tick)
            .start();

    var link = svg.selectAll(".link")
            .data(graphData.links)
            .enter().append("line")
            .attr("class", "link");

    var node = svg.selectAll(".node")
            .data(graphData.nodes)
            .enter().append("g")
            .attr("class", "node")
//            .style("fill", function(d) { return color(d.group); })
            .call(force.drag);

    // add the text
    node.append("text")
            .attr("dx", 12)
            .attr("dy", ".35em")
            .text(function(d) { return d.username });

    // add the nodes
    node.append("circle")
            .attr("r", 8)
            .style("fill", function(d) { return color(d.group); });

    force.on("tick", function() {
        link.attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

        node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
    });

</script>
</body>