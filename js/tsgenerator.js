$(document).ready(function() {
    
    for(i=0;i<tagsSeries.length;i++) {
        $('.selectcorr').append('<option value="' + tagsSeries[i].s_id + '">' + tagsSeries[i].name + '</option>');
    }
    
    $("#submitTS").click(function(){
        $('.selectcorr').removeClass('is-invalid');
        var opt1 = $('#corr_1').val();
        var opt2 = $('#corr_2').val();
        
        if (opt1 === undefined || opt2 === undefined) {
            $('.selectcorr').addClass('is-invalid');
            $( "#errormessageTS" ).show();
            $( "#errormessageTS" ).text('Inputs cannot be empty!');
        }
        else if (opt1 === opt2) {
            $('.selectcorr').addClass('is-invalid');
            $( "#errormessageTS" ).show();
            $( "#errormessageTS" ).text('Inputs cannot be identical!');
        }
        else {
            
            $.ajax({
                url: 'routerAjax.php',
                type: 'POST',
                data: {
                    model: ['get_hist_correl_by_s_id'],
                    toScript: ['histCorrel'],
                    fromAjax: {'s_id_1': opt1,'s_id_2':opt2}
                    },
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(res){
                    if (isJson(res)) {
                        histCorrel = JSON.parse(res).histCorrel;
                        drawTS(histCorrel);
                    } else {
                        console.log('Not Json');
                        console.log(res);
                    }
                },
                error:function(){
                    validateFail('Historical data not found.');
                }
            });

            
        }
        
        
        function drawTS(histCorrel) {
            name1 = $('#corr_1').find('option:selected').text();
            name2 = $('#corr_2').find('option:selected').text();
            
            console.log(histCorrel);
            window.histCorrel = histCorrel;
            
            var histCorrelClean = [];
            var j = 0;
            
            /*var histCorrelHistogram = [];
            histCorrelHistogram[0] = {}; histCorrelHistogram[1] = {}; histCorrelHistogram[2] = {}; histCorrelHistogram[3] = {};
                    histCorrelHistogram[0].name = '.75 to 1'; histCorrelHistogram[0].data = []; histCorrelHistogram[0].data[0] = 0;
                    histCorrelHistogram[1].name = '.5 to .75'; histCorrelHistogram[1].data = []; histCorrelHistogram[1].data[0] = 0;
                    histCorrelHistogram[2].name = '.25 to .5'; histCorrelHistogram[2].data = []; histCorrelHistogram[2].data[0] = 0;
                    histCorrelHistogram[3].name = '0 to .25'; histCorrelHistogram[3].data = []; histCorrelHistogram[3].data[0] = 0;
*/
            for (i=0;i<histCorrel.length;i++) {
                if (histCorrel[i].h_pretty_date_timestamp == undefined || histCorrel[i].h_value == undefined) continue;
                histCorrelClean[j] = [];
                histCorrelClean[j][0] = parseInt(histCorrel[i].h_pretty_date_timestamp);
                histCorrelClean[j][1] = parseFloat(histCorrel[i].h_value);
                
                /*
                if (histCorrelClean[j][1] > 0.75) {
                    histCorrelHistogram[0].data[0]++;
                } else if (histCorrelClean[j][1] > 0.5) {
                    histCorrelHistogram[1].data[0]++;
                } else if (histCorrelClean[j][1] > 0.25) {
                    histCorrelHistogram[2].data[0]++;
                } else if (histCorrelClean[j][1] > 0) {
                    histCorrelHistogram[3].data[0]++;
                }*/
                
                
                j++;

            }
            console.log(histCorrelClean);

            
            Highcharts.stockChart('tsChart', {
                chart: {
                    marginRight: 10,
                    backgroundColor: 'rgba(225, 233, 240,.6)',
                    plotBackgroundColor: '#FFFFFF',
                    plotBorderColor: '#C0C0C0',
                    //plotBorderWidth: 1,
                    height:500
                },
                title: {
                    text: 'Trailing 30-day correlation between ' + name1 + ' and ' + name2
                },
                credits: {
                    enabled: false
                },
                exporting :{
                    enabled: false
                },
                rangeSelector : {
                    selected: 4,
                    
                    buttonTheme: {
                        width:60
                    },
                    
                    buttons: [
                        {                                                 
                            type: 'month',
                            count: 1,
                            text: '1mo'
                        },
                        {                                                 
                        type: 'month',
                            count: 3,
                            text: '3mo'
                        },
                        {
                            type: 'ytd',
                            count: 1,
                            text: 'YTD'
                        },
                        {
                            type: 'year',
                            count: 1,
                            text: '1Y'
                        },
                        {
                            type: 'all',
                            text: 'All'
                        }
                        ]
                },
                xAxis: {
                    type: 'datetime',
                    dateTimeLabelFormats: { 
                        day: "%b %e",
                        week: "%b %e",
                        month: "%b %Y"
                    },
                    gridLineWidth: 1,
                },
                yAxis: {
                    title: {
                        text: 'Rolling 30-day Correlation',
                    },
                    opposite: false
                },
                plotOptions: {
                    series: {
                        dataGrouping: {
                                units: [[
                                        'day',
                                        [1]
                                ]]
                        }
                    },
                    line: {
                        turboThreshold: 0
                    },
                    column: {
                        stacking: 'normal'
                    }
                },
                series: [{
                    color : 'darkblue',
                    shadow : true,
                    type: 'line',
                    id: 'correl',
                    data: histCorrelClean,
                    turboThreshold: 0
                },
                {
                    type: 'sma',
                    linkedTo: 'correl',
                    params: {
                        period: 90  
                    },
                    zIndex: 1,
                    marker: {
                        enabled: false
                    }
                },
                {
                    type: 'ema',
                    linkedTo: 'correl',
                    params: {
                        //period: 90  
                    },
                    zIndex: 1
                },              
                {
                    type: 'bb',
                    linkedTo: 'correl',
                    params: {
                        //period: 90  
                    },
                    zIndex: 1
                }

                ]
            });

            
            
        }
    
        
    });



});
