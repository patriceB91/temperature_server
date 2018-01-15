// Color set
var blue = '#7ab5f0';
var green = '#7af07a';
var red = '#f07a7a';

var gaugeOptions = {

    chart: {
        type: 'solidgauge'
    },

    title: null,

    pane: {
        center: ['50%', '85%'],
        size: '100%',
        startAngle: -90,
        endAngle: 90,
        background: {
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
            innerRadius: '60%',
            outerRadius: '100%',
            shape: 'arc'
        }
    },

    tooltip: {
        enabled: false
    },

    // the value axis
    yAxis: {
        stops: [
            [0.1, green],   // green
            [0.5, blue],    // yellow
            [0.9, red]      // red
        ],
        lineWidth: 0,
        minorTickInterval: null,
        tickAmount: 2,
        title: {
            y: -70
        },
        labels: {
            y: 16
        }
    },

    plotOptions: {
        solidgauge: {
            dataLabels: {
                y: 5,
                borderWidth: 0,
                useHTML: true
            }
        }
    }
};

// The Pool Temp gauge
var poolTemp = Highcharts.chart('pooltemp', Highcharts.merge(gaugeOptions, {
    yAxis: {
        min: -10,
        max: 40,
        title: {
            text: 'Item'
        }
    },

    credits: {
        enabled: false
    },

    series: [{
        name: 'Pool Temp',
        data: [30],
        dataLabels: {
            format: '<div style="text-align:center"><span style="font-size:25px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                   '<span style="font-size:12px;color:silver">°C</span></div>'
        },
        tooltip: {
            valueSuffix: ' °C'
        }
    }]

}));

var airTemp = Highcharts.chart('airtemp', Highcharts.merge(gaugeOptions, {
    yAxis: {
        min: 0,
        max: 35,
        title: {
            text: 'Temp'
        }
    },

    credits: {
        enabled: false
    },

    series: [{
        name: 'Air Temp',
        data: [30],
        dataLabels: {
            format: '<div style="text-align:center"><span style="font-size:25px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                   '<span style="font-size:12px;color:silver">°C</span></div>'
        },
        tooltip: {
            valueSuffix: ' °C'
        }
    }]

}));

$(document).ready(function () {
    getData();
    $("#refresh").on('click', function () {
        getData();
    });

    var chart = c3.generate({
        bindto: '#c3temp',
        data: {
          columns: [
            ['data1', 30, 200, 100, 400, 150, 250],
            ['data2', 50, 20, 10, 40, 15, 25]
          ]
        }
    });

});

function getData() {
    var url = 'http://192.168.0.10/home_temp/tempAPI.php?start=2012-12-30&end=2013-01-04';

    var period = $( "#period" ).val();
    $.getJSON(url, {period: period}, function (response) {
        /*
         * Get the lats value from the series, to set the gauge
         */
        var ptTemp = poolTemp.series[0].points[0];
        var lstItem = response.data[1].data.length-1;
        ptTemp.update(response.data[1].data[lstItem]);

        ptTemp = airTemp.series[0].points[0];
        lstItem = response.data[2].data.length-1;
        ptTemp.update(response.data[2].data[lstItem]);

        var gaugeName = poolTemp.yAxis;
        poolTemp.yAxis[0].update({title: 
                                    {
                                    text: response.data[1].name
                                    }
                                });
        
        airTemp.yAxis[0].update({title: 
                                    {
                                    text: response.data[2].name
                                    }
                                });                        

        var options = {
            title: {
                text: 'Pool air & water temperatures'
            },
            chart: {
                renderTo: 'container',
                type: 'line',
                color: '#7ab5f0'
            },
            // F0B27A
            xAxis: {
                type: 'datetime',
                categories: response.data[0].labels,
                labels: {
                    rotation: -90
                }
            },
            yAxis: {
                title: {
                    text: 'Temp'
                }

            },
            series: [{
                data: response.data[1].data,
                name: response.data[1].name
            },
            {
                data: response.data[2].data,
                name: response.data[2].name
            }
        ]

        };
        chart = new Highcharts.Chart(options);
    });

}
