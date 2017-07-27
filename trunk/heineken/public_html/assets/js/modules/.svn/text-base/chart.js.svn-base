/*Highchart Helper*/
/*cendekiapp*/


/*mandatory variable req
req.container
req.chart_data
req.categories*/

function line_chart(req){
	$(req.container).highcharts({
		chart: req.chart,
	    title: {
	        text: '',
	        x: -20 //center
	    },
	    xAxis: {
	        categories: req.categories
	    },
	    yAxis: {
	        title: {
	            text: 'Number'
	        },
	        plotLines: [{
	            value: 0,
	            width: 1,
	            color: '#808080'
	        }]
	    },
	     credits: {
	          enabled: false
	      },

	    tooltip: {
	        valueSuffix: ''
	    },
	    legend: {
	        layout: 'vertical',
	        align: 'right',
	        verticalAlign: 'middle',
	        borderWidth: 0
	    },
	    series: req.chart_data
	});
}

function pie_chart(req){
	$(req.container).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
        },
         credits: {
              enabled: false
          },

        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    distance: -30,
                    color: '#ffffff',
                    format: '{point.percentage:.1f} %'
                },
                showInLegend: true
            }
        },
        series: req.chart_data
    });
}

function column_chart(req){
	$(req.container).highcharts({
            chart: {
                type: 'column',
                zoomType: 'xy'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: req.categories
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Volume'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Volume'
                }
            },
            credits: {
                  enabled: false
              },

            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"> <b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: req.chart_data
        });
}

function stack_column_chart(req){
    $(req.container).highcharts({
            chart: {
                type: 'column',
                zoomType: 'xy'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: req.categories
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Volume'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Volume'
                }
            },
            credits: {
                  enabled: false
              },
            legend: false,
            tooltip: {
               formatter: function() {
                    return '<b>'+ this.point.name+'</b><br/>'+
                        '<span style="color:{this.series.color};">'+this.series.name +'</span>: '+ number_format(this.y) +'<br/>'+
                        'Total: '+ number_format(this.point.stackTotal);
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                   
                }
            },
            series: req.chart_data
        });
}

function bar_chart(req){
    $(req.container).highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: req.categories,
            title: {
                text: null
            }
        },
         credits: {
              enabled: false
          },

        yAxis: {
            min: 0,
            title: {
                text: 'Volumes',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 100,
            floating: true,
            borderWidth: 1,
            backgroundColor: '#FFFFFF',
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: req.chart_data
    });
}
