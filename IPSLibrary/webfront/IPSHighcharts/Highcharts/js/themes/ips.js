/**
 * IPS theme for Highcharts JS
 * @author sanman
 */

Highcharts.theme = {
//    colors: ["#DDDF0D", "#7798BF", "#55BF3B", "#DF5353", "#aaeeee", "#ff0066", "#eeaaee", 
//        "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
    
        
    chart: {
        backgroundColor: 'transparent',
        borderWidth: 0,
        borderRadius: 0,
        plotBackgroundColor: null,
        plotShadow: false,
        plotBorderWidth: 0,
        renderTo: 'container',
        zoomType: 'xy'

    },
    credits: {
      enabled: false
        },
    title: {
        style: { 
            color: '#FFF',
            font: 'normal 12px Arial, sans-serif'
        }
    },
    subtitle: {
        style: { 
            color: '#DDD',
            font: '12px Arial, sans-serif'
        }
    },
    xAxis: {
        endOnTick: false,
        gridLineColor: 'rgba(20, 150, 100, .2)',
        gridLineWidth: 1,
        minorGridLineColor: 'rgba(180, 190, 50, .05)',
        minorTickInterval: 'auto',
        minorGridLineWidth: 1,
        lineColor: '#999',
        tickColor: '#999',
        labels: {
            style: {
                color: '#999',
                fontWeight: 'light'
            }
        },
        title: {
            style: {
                color: '#AAA',
                font: 'bold 12px Arial, sans-serif'
            }                
        }
    },
    yAxis: {
        alternateGridColor: null,
        minorTickInterval: null,
        gridLineColor: 'rgba(20, 150, 100, .2)',
        gridLineWidth: 1,
        lineWidth: 0.5,
        tickWidth: 0,
        minorGridLineColor: 'rgba(180, 190, 50, .05)',
        minorTickInterval: 'auto',
        minorTickColor: 'rgba(180, 190, 50, .1)',
        minorTickWidth: 1,
        minorGridLineWidth: 1,
        minorTickLength: 3,    
        labels: {
            style: {
                color: '#999',
                fontWeight: 'normal'
            }
        },
        title: {
            style: {
                color: '#AAA',
                font: 'bold 12px Arial, sans-serif'
            }                
        }
    },
    legend: {
        borderColor: 'transparent',
        
        itemStyle: {
            color: '#CCC'
        },
        itemHoverStyle: {
            color: '#FFF'
        },
        itemHiddenStyle: {
            color: '#333'
        },
    },
    
    
    
    labels: {
        style: {
            color: '#CCC'
        }
    },
    tooltip: {
    enabled:true,
        crosshairs: [{
            width: 1,
            color: 'rgba(110, 180, 50, .5)'
            },
        {   width: 1,
            color: 'rgba(180, 190, 50, .5)'
        }],
        shadow: false,
        borderColor: 'rgba(0, 0, 0, 0)',
        backgroundColor: 'rgba(0, 0, 0, 0)',
        
        style: {
            color: 'rgba(0, 0, 0, 0)',
    //        padding: 10,
    //        font: 'light 8px Arial, sans-serif'
            
        }
    },
    
    plotOptions: {
        
                
        line: {
            dataLabels: {
                color: '#CCC'
                
            },
            marker: {
                lineColor: '#333'
            }
        },
        spline: {
            marker: {
                lineColor: '#333'
            }
        },
        scatter: {
            marker: {
                lineColor: '#333'
            }
        }
    },
    
    toolbar: {
        itemStyle: {
            color: '#CCC'
        }
    },
    
    navigation: {
        buttonOptions: {
            backgroundColor: {
                linearGradient: [0, 0, 0, 20],
                stops: [
                    [0.4, '#606060'],
                    [0.6, '#333333']
                ]
            },
            borderColor: '#000000',
            symbolStroke: '#C0C0C0',
            hoverSymbolStroke: '#FFFFFF'
        }
    },
    
    exporting: {
        enabled: false,
        },    
//        buttons: {
//            exportButton: {
    //            symbolFill: '#55BE3B'
        //    },
        //    printButton: {
        //        symbolFill: '#7797BE'
        //    }
    //    } 
        
//    },    
    
    // special colors for some of the demo examples
//    legendBackgroundColor: 'rgba(48, 48, 48, 0.8)',
//    legendBackgroundColorSolid: 'rgb(70, 70, 70)',
//    dataLabelsColor: '#444',
//    textColor: '#E0E0E0',
//    maskColor: 'rgba(255,255,255,0.3)'
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);  