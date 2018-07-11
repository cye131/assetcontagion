$(document).ready(function() {
    
    
    
    (function() {
        var corr_type = sessionStorage.getItem('corr_type') || 'rho';
        var freq = sessionStorage.getItem('freq') || 'd';
        var trail = sessionStorage.getItem('trail') || 30;
        var data = {};
        $('#overlay').show();
    
        var d1 = $.Deferred(function(dfd) {
            var ajaxGetHistCorrIndex = getAJAX(['get_hist_corr_index'],[],['histCorrIndex'],{'category': null, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000,1);
            ajaxGetHistCorrIndex.done(function(res) {
                var histCorrIndex = JSON.parse(res).histCorrIndex;
                data.histCorrIndex = histCorrIndex;
                drawFCI(histCorrIndex);
              });
            dfd.resolve(data);
            return dfd.promise();
        });

        $.when(d1).done(function(data) {
            setData(data);
          $('#overlay').hide();
        });

    })();

    
    
    


});




function drawFCI(histCorrIndex) {
    var hmd = [];
    var vals = [];
    for(i=0;i<histCorrIndex.length;i++) {
        hmd.push({
            "x": new Date(histCorrIndex[i].pretty_date).getTime(),
            "y": parseFloat(histCorrIndex[i].value)
        });
        vals.push( parseFloat(histCorrIndex[i].value) );
    }

    //var max = Math.max(...vals);
    //var min = Math.min(...vals);

    var hmcol = [];
    for(i=0;i<histCorrIndex.length;i++) {
        hmcol.push({
            "x": new Date(histCorrIndex[i].pretty_date).getTime(),
            "low": -1,
            "high": 1
        });
    }
    
    // Add one false date for padding
    hmcol.push({
        "x": hmcol[hmcol.length-1].x +1000*60*60*24,
        "low": null,
        "high": null
    });

    var o = getHighChartsOptions();
    
    $.extend(true,o,{
        chart: {
            backgroundColor: 'rgba(255,255,255,0)',
            marginRight: 50,
            marginLeft: 50
        },
        credits: {
            enabled: false  
        },
        title: {
            text: 'Average ' + (sessionStorage.getItem('trail') || '30') + (sessionStorage.getItem('freq') || 'd') + ' cross-regional equity correlation (' + (getCorrName(sessionStorage.getItem('corr_type')) || 'Pearson Correlation') + ')',
            useHTML: true
        },
    
        subtitle: {
            text: null
        },
        plotOptions: {
                series: {
                    dataGrouping: {
                        enabled: true,
                        units: [ ['day',[1]]]
                    },
                    cursor: 'pointer',
                    point: {
                    }
                }
                
        },
        tooltip: {
            useHTML: true,
            formatter: function () {
                return '<h6 class="text-center;" style="font-weight:bold">'+ Highcharts.dateFormat('%b \ %e \ %Y', this.points[0].x) + '</h6></br>' +
                'Average Correlation: ' + Highcharts.numberFormat(this.points[0].y,4);
            },
            shared: true
        },
        yAxis: {
            max: 1,//max,
            min: -1,//min,
            startOnTick: false,
            endOnTick: false,
            opposite:false,
            showLastLabel: true,
            labels: {
                formatter: function () {
                    return this.value.toFixed(1);
                }
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: 'black',
                dashStyle: 'Dash',
                zIndex: 2
                },{
                value: 1,
                width: 1,
                color: 'rgba(10, 24, 66,1)',
                zIndex: 5
                },{
                value: -1,
                width: 1,
                color: 'rgba(10, 24, 66,1)',
                zIndex: 5
                }
            ]
        },
        xAxis: {
            dateTimeLabelFormats: {
                day: "%m-%d-%Y",
                week: "%m-%d-%Y"
            },
            
            labels: {
            }
        },
        navigator: {
            enabled: false
        },
        series: [{
            data: hmd,
            turboThreshold: 0,
            id: 's01',
            type: 'line',
            color: '#333',
            marker: {
                enabled: false
            },
            zIndex: 1,

        },
        {
            type: 'arearange',
            data: hmcol,
            turboThreshold: 0,
            linkedTo: 's01',
            zIndex: 0,
            fillColor: 'rgba(255,255,255,0)',
            lineColor: 'rgba(255,255,255,0)'
        },{
            type: 'sma',
            linkedTo: 's01',
            params: {
                period: 90  
            },
            zIndex: 1,
            marker: {
                enabled: false
            },
            visible: false
        }]
    });
    

    var c = new Highcharts.stockChart('chart-fci', o);

}