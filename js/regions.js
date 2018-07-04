$(document).ready(function() {

    

    (function() {
        var category = 'reg';
        var corr_type = sessionStorage.getItem('corr_type') || 'rho';
        var freq = sessionStorage.getItem('freq') || 'd';
        var trail = sessionStorage.getItem('trail') || 30;

        var ajaxGetSpecsCategories = getAJAX(['get_specs_categories'],[],['specsCategories'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000);
        var ajaxGetTagsSeries = getAJAX(['get_tags_series'],[],['tagsSeries'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000);
        var ajaxGetTagsCorrel = getAJAX(['get_tags_correl'],[],['tagsCorrel'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000);

        $.when(ajaxGetSpecsCategories,ajaxGetTagsSeries, ajaxGetTagsCorrel).done(function(r1, r2, r3) {
            var data= {
                dataInfo: {"category": category, "corr_type": corr_type, "freq": freq, "trail": trail},
                specsCategories: JSON.parse(r1[0]).specsCategories,
                tagsSeries: JSON.parse(r2[0]).tagsSeries,
                tagsCorrel: JSON.parse(r3[0]).tagsCorrel,
                tagsGFI: []
            };
            $.each(data.tagsCorrel, function(i,row) {
                if (row.grouping_1 === 'World.' || row.grouping_2 === 'World.') data.tagsGFI.push(row);
            });
            $('#data').data(data);
            setCategoryOptions(data);
            drawHeatMap(data.tagsSeries,data.tagsCorrel);

            drawMaps(data.tagsGFI);
            drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,0.75);
            drawStrongCorrelationsEurope(data.tagsSeries,data.tagsCorrel,0.75);
            $("#highMapEurope > div").addClass('float-right');
            $("#highMapEurope > div").css('margin-top','-60px');
        });
        
        
        var ajaxGetHistCorrIndex = getAJAX(['get_hist_corr_index'],[],['histCorrIndex'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000);
        ajaxGetHistCorrIndex.done(function(res) {
            var histCorrIndex = JSON.parse(res).histCorrIndex;
            drawHeatMapDates(histCorrIndex);

            var dates = [];
            $.each(histCorrIndex, function(i,row) {
                dates.push(row.pretty_date);
            });
            $('#data').data('dates',dates.reverse());
        });
        
        
        window.playState = 0; // 0 -> never played, 1 -> paused, 2-> playing
        window.playIndex = 0;
        window.dateCount = [];
        
        

    })();

         
                 
    $('#heatmap').on('click', '#playHistorical', function() {
        if (window.playState === 0) {
            $('#overlay').show();
            $('#loadmessage').text('Loading data...');
            
            window.playState = 2;
            
            // Get all Mondays and display result
            // SO console doensn't show all results
            //var mondays = getMondays(new Date(2010,0,1));
            // Count of Mondays, not all shown in SO console
            //console.log('There are ' + mondays.length + ' Mondays, the first is ' + mondays[0].toString());
           // window.dateCount = mondays;
           window.dateCount = $('#data').data().dates;
            getHistCorrel(window.dateCount,0);
            
            $('#overlay').hide();
            $("#playHistorical").html('&#10074;&#10074; Click to pause');
        }
        else if (window.playState === 1) {
            window.playState = 2;
            getHistCorrel(window.dateCount,window.playIndex);
            
            $("#playHistorical").html('&#10074;&#10074; Click to pause');
        }
        else if (window.playState === 2) {
            window.playState = 1;
            
            $("#playHistorical").html('&#9654; Click to resume');
        } 
    });



    //Resizes width after clicking each tab, otherwise highcharts is too skinny!
    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        if ($('.successmsg').text().length > 0) {
        $( '.chart' ).each(function() { 
            $(this).highcharts().reflow();
        });
        }
    });
    
    
    $("#corrselector").submit(function(e){
        e.preventDefault();
        var freqtrail = $('#freqtrail').val();
        freqtrail = freqtrail.split('.');
        
        $('#freq').val(freqtrail[0]);
        $('#trail').val(freqtrail[1]);
        window.sessionStorage.setItem("freq",freqtrail[0]);
        window.sessionStorage.setItem("trail",freqtrail[1]);
        window.sessionStorage.setItem("corr_type", $('#corr_type').val() );
        
        $("#corrselector")[0].submit();
    });


});

    
function setCategoryOptions(data) {
    if (data.specsCategories.length !== 1) console.log('ERR: No category selected');
    else {
        var freqtrails = data.specsCategories[0].cat_freqtrails.split(',');
        var selectedfreqtrail =  (typeof sessionStorage.getItem('freq') !== undefined && typeof sessionStorage.getItem('trail') !== undefined) ? sessionStorage.getItem('freq') + '.' + sessionStorage.getItem('trail') : null;
        for (i=0;i<freqtrails.length;i++) {
            var freqtrails_split = freqtrails[i].split('.');
            var freqtrails_str = freqtrails_split[1] + (freqtrails_split[0] === 'd' ? '-day' : freqtrails_split[0]) + ' ' + (freqtrails_split[0] === 'd' ? 'Rolling Correlation' : 'Correlation') ;
            $('#freqtrail').append('<option value="' + freqtrails[i] + '" ' + (freqtrails[i] === selectedfreqtrail ? 'selected' : '')  + ' >' + freqtrails_str + '</option>');
        }
        
        var corr_types = data.specsCategories[0].cat_corrtypes.split(',');
        var selectedcorr_type =  (typeof sessionStorage.getItem('corr_type') !== undefined) ? sessionStorage.getItem('corr_type') : null;
        for (i=0;i<corr_types.length;i++) {
            var corr_type_str = (corr_types[i] === 'rho' ? 'Pearson Correlation' : (corr_types[i] === 'ktau' ? "Kendall's Tau" : '')) ;
            $('#corr_type').append('<option value="' + corr_types[i] + '" ' +  (corr_types[i] === selectedcorr_type ? 'selected' : '') + ' >' + corr_type_str + '</option>');
        }

    }
}


// Get all Mondays from start date to today
// Default today's date
function getMondays(d) {
  // Set to first Monday
  d.setDate(d.getDate() + (8 - (d.getDay() || 7)) % 7);
  var mondays = [dateToYmd(d)];

  // Create Dates for all Mondays up to end year and month
  while (d < new Date() ) {
    var m = new Date(d.setDate(d.getDate() + 7));
    mondays.push(dateToYmd(m));
  }
  mondays.pop();
  return mondays;
}

function dateToYmd(date) {         
    var yyyy = date.getFullYear().toString();                                    
    var mm = (date.getMonth()+1).toString(); // getMonth() is zero-based         
    var dd  = date.getDate().toString();             
    return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
}



function getHistCorrel(dateCount,i) {
    window.playIndex = i;
    if (window.dateCount === undefined || window.playIndex === undefined) return;
    if (window.playState !== 2) {
        return;
    }
    
    var date = dateCount[i];
    //console.log(date);
    var data = $('#data').data();
    
    var ajaxGetHistCorrel = getAJAX(
                                    ['get_hist_correl_by_date'],[],['tagsCorrel',],
                                    {"date": date, "category": data.dataInfo.category, "corr_type": data.dataInfo.corr_type, "freq": data.dataInfo.freq, "trail": data.dataInfo.trail},
                                    20000,'disabled');
    
    ajaxGetHistCorrel.done(function(res) {
        console.log(res);
            if (JSON.parse(res).tagsCorrel.length > 0) {
                var tagsCorrelDate = JSON.parse(res).tagsCorrel;
                var hm = drawHeatMap(data.tagsSeries,tagsCorrelDate,1);
                
                
                chart = $("#heatmap").highcharts();

                for (j=0;j<hm.data.length;j++) {
                    chart.series[0].data[j].update(hm.data[j],false);
                }
                chart.redraw();
                
                
                
                $("#date").remove();
                chart.renderer.text(date,chart.plotLeft+(chart.plotWidth-chart.plotLeft)/2,chart.plotTop + (chart.plotHeight-chart.plotTop)/2) //(text,x,y)
                .attr({
                    zIndex: 4,
                    'text-anchor': 'middle',
                    id: 'date'
                })
                .css({
                    textAlign: 'center',
                    color: 'white',
                    fontSize: '36px',
                    opacity: 0.7,
                    'text-shadow': '-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black'
                })
                .add();
            }
            
            if (window.playIndex < dateCount.length-1) {
                //window.playIndex++;
                window.playIndex = window.playIndex + 5; //skip by week
                setTimeout(function() { getHistCorrel(dateCount,window.playIndex); }, 200);
            } else {
                window.playIndex = 0;
            }
    });
}





function getHistCorrelSingle(date) {
    console.log(date);
    var data = $('#data').data();
    
    var ajaxGetHistCorrel = getAJAX(
                                    ['get_hist_correl_by_date'],[],['tagsCorrel',],
                                    {"date": date, "category": data.dataInfo.category, "corr_type": data.dataInfo.corr_type, "freq": data.dataInfo.freq, "trail": data.dataInfo.trail},
                                    20000,'disabled');
    
    ajaxGetHistCorrel.done(function(res) {
        console.log(res);
            if (JSON.parse(res).tagsCorrel.length > 0) {
                var tagsCorrelDate = JSON.parse(res).tagsCorrel;
                var hm = drawHeatMap(data.tagsSeries,tagsCorrelDate,1);
                
                
                chart = $("#heatmap").highcharts();

                for (j=0;j<hm.data.length;j++) {
                    chart.series[0].data[j].update(hm.data[j],false);
                }
                chart.redraw();
                
                $("#date").remove();
                chart.renderer.text(date,chart.plotLeft+(chart.plotWidth-chart.plotLeft)/2,chart.plotTop + (chart.plotHeight-chart.plotTop)/2) //(text,x,y)
                .attr({
                    zIndex: 4,
                    'text-anchor': 'middle',
                    id: 'date'
                })
                .css({
                    textAlign: 'center',
                    color: 'white',
                    fontSize: '36px',
                    opacity: 0.7,
                    'text-shadow': '-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black'
                })
                .add();
            }
            
    });
}

