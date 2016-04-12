(function($) {

    'use strict';
    $(document).ready(function() {
        //Get from JSON data and build
        d3.json('/assets/charts/sales.json', function(data) {

            //Widget venite giornaliere
            nv.addGraph(function() {
                var chart = nv.models.lineChart()
                    .x(function(d) {
                        return d[0]
                    })
                    .y(function(d) {
                        return d[1]
                    })
                    .color(['#000'])
                    .margin({
                        top: 10,
                        right: -10,
                        bottom: -13,
                        left: -10
                    })
                    .showXAxis(false)
                    .showYAxis(false)
                    .showLegend(false)
                    .interactive(false);

                d3.select('.widget-8-chart svg')
                    .datum(data.siteVisits)
                    .call(chart);

                nv.utils.windowResize(chart.update);

                nv.utils.windowResize(function() {
                    setTimeout(function() {
                        $('.widget-8-chart .nvd3 circle.nv-point').attr("r", "3").css({
                            'stroke-width': '2px',
                            ' stroke-opacity': 0.4
                        });
                    }, 500);
                });

                return chart;
            }, function() {
                setTimeout(function() {
                    $('.widget-8-chart .nvd3 circle.nv-point').attr("r", "3").css({
                        'stroke-width': '2px',
                        ' stroke-opacity': 0.4
                    });
                }, 500);
            });
        });

	    //NVD3 Charts
	    d3.json('/assets/charts/charts.json', function(data) {

		    //Grafico del widget "vendite"
		    (function() {
			    nv.addGraph(function() {
				    var chart = nv.models.lineChart()
					    .x(function(d) {
						    return d[0]
					    })
					    .y(function(d) {
						    return d[1]
					    })
					    .color([
						    $.Pages.getColor('success'), //vendite
						    $.Pages.getColor('complete'),
						    $.Pages.getColor('complete'),
						    $.Pages.getColor('primary') //clienti
					    ])
					    .showLegend(false)
					    .margin({
						    left: 30,
						    bottom: 35
					    })
					    .useInteractiveGuideline(true);

				    chart.xAxis
					    .tickFormat(function(d) {
						    return d3.time.format('%a')(new Date(d))
					    });

				    chart.yAxis.tickFormat(d3.format('d'));

				    d3.select('.nvd3-line svg')
					    .datum(data.nvd3.line)
					    .transition().duration(500)
					    .call(chart);

				    nv.utils.windowResize(chart.update);

				    $('.nvd3-line').data('chart', chart);

				    return chart;
			    });
		    })();

		    //Widget visitatori
		    (function() {
			    var container = '.widget-15-chart';

			    var seriesData = [
				    [],
				    []
			    ];
			    var random = new Rickshaw.Fixtures.RandomData(40);
			    for (var i = 0; i < 40; i++) {
				    random.addData(seriesData);
			    }

			    var graph = new Rickshaw.Graph({
				    renderer: 'bar',
				    element: document.querySelector(container),
				    height: 200,
				    padding: {
					    top: 0.5
				    },
				    series: [{
					    data: seriesData[0],
					    color: $.Pages.getColor('complete-light'),
					    name: "Nuovi utenti"
				    }, {
					    data: seriesData[1],
					    color: $.Pages.getColor('master-lighter'),
					    name: "Utenti di ritorno"
				    }]
			    });

			    var hoverDetail = new Rickshaw.Graph.HoverDetail({
				    graph: graph,
				    formatter: function(series, x, y) {
					    var date = '<span class="date">' + new Date(x * 1000).toUTCString() + '</span>';
					    var swatch = '<span class="detail_swatch" style="background-color: ' + series.color + '"></span>';
					    var content = swatch + series.name + ": " + parseInt(y) + '<br>' + date;
					    return content;
				    }
			    });

			    graph.render();

			    $(window).resize(function() {
				    graph.configure({
					    width: $(container).width(),
					    height: 200
				    });

				    graph.render()
			    });

			    $(container).data('chart', graph);
		    })();
	    });

        // Init portlets

        var bars = $('.widget-loader-bar');
        var circles = $('.widget-loader-circle');
        var circlesLg = $('.widget-loader-circle-lg');
        var circlesLgMaster = $('.widget-loader-circle-lg-master');

        bars.each(function() {
            var elem = $(this);
            elem.portlet({
                progress: 'bar',
                onRefresh: function() {
                    setTimeout(function() {
                        elem.portlet({
                            refresh: false
                        });
                    }.bind(this), 2000);
                }
            });
        });


        circles.each(function() {
            var elem = $(this);
            elem.portlet({
                progress: 'circle',
                onRefresh: function() {
                    setTimeout(function() {
                        elem.portlet({
                            refresh: false
                        });
                    }.bind(this), 2000);
                }
            });
        });

        circlesLg.each(function() {
            var elem = $(this);
            elem.portlet({
                progress: 'circle-lg',
                progressColor: 'white',
                overlayColor: '0,0,0',
                overlayOpacity: 0.6,
                onRefresh: function() {
                    setTimeout(function() {
                        elem.portlet({
                            refresh: false
                        });
                    }.bind(this), 2000);
                }
            });
        });


        circlesLgMaster.each(function() {
            var elem = $(this);
            elem.portlet({
                progress: 'circle-lg',
                progressColor: 'master',
                overlayOpacity: 0.6,
                onRefresh: function() {
                    setTimeout(function() {
                        elem.portlet({
                            refresh: false
                        });
                    }.bind(this), 2000);
                }
            });
        });

    });

})(window.jQuery);