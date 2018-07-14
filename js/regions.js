$(document).ready(function() {


    (function() {
      
        var category = 'reg';
        var corr_type = sessionStorage.getItem('corr_type') || 'rho';
        var freq = sessionStorage.getItem('freq') || 'd';
        var trail = sessionStorage.getItem('trail') || 30;
        var data = {};
        $('#overlay').show();

        
        /*
        //Use locally stored data if it exists and has not expired yet (10 mins within fetch-time)
        var browserStorage = localStorage.getItem('data');
        var browserStorageExpTime = localStorage.getItem('data-exp-time');
        if ( (browserStorage !== null && isJson(browserStorage) === true) && (browserStorageExpTime  !== null && new Date(parseInt(browserStorageExpTime)) > new Date()) ) {
          $.extend(true,data,JSON.parse(localStorage.getItem('data')));
          
          if (data.tagsCorrel != null && data.specsCategories != null && data.tagsSeries != null && data.tagsCorrel != null && data.tagsGFI != null && data.hmDates.length != 0 && data.histCorrIndex.length != 0) {
          
            setCategoryOptions(data);
  
            if (window.location.href.indexOf('hm') !== -1) {
              drawHeatMap(data.tagsSeries,data.tagsCorrel);
              drawHeatMapDates(data.histCorrIndex);
            }
            else if (window.location.href.indexOf('map') !== -1) {
              drawMaps(data.tagsGFI);
              drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,0.75);
              drawStrongCorrelationsEurope(data.tagsSeries,data.tagsCorrel,0.75);
            }
            
            setData(data);
            $('#overlay').hide();
            return; 
          }
          
        }
        */
        
        

        var d1 = $.Deferred(function(dfd) {
          var ajaxGetSpecsCategories = getAJAX(['get_specs_categories'],[],['specsCategories'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000,1);
          var ajaxGetTagsSeries = getAJAX(['get_tags_series'],[],['tagsSeries'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000,1);
          var ajaxGetTagsCorrel = getAJAX(['get_tags_correl'],[],['tagsCorrel'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000,1);
  
          $.when(ajaxGetSpecsCategories,ajaxGetTagsSeries, ajaxGetTagsCorrel).done(function(r1, r2, r3) {
              $.extend(true,data,{
                  dataInfo: {"category": category, "corr_type": corr_type, "freq": freq, "trail": trail},
                  specsCategories: JSON.parse(r1[0]).specsCategories,
                  tagsSeries: JSON.parse(r2[0]).tagsSeries,
                  tagsCorrel: JSON.parse(r3[0]).tagsCorrel,
                  tagsGFI: []
              });
              $.each(data.tagsCorrel, function(i,row) {
                  if (row.grouping_1 === 'World.' || row.grouping_2 === 'World.') data.tagsGFI.push(row);
              });
              setCategoryOptions(data);
              
              if (window.location.href.indexOf('hm') !== -1) {
                drawHeatMap(data.tagsSeries,data.tagsCorrel);
              }
              else if (window.location.href.indexOf('map') !== -1) {
                var charts = drawMaps(data.tagsGFI);
                drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,0.75,charts[0]);
                drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,0.75,charts[1]);
              }
              
              dfd.resolve(data);
              return dfd.promise();
          });
        });
        
        var d2 = $.Deferred(function(dfd) {
          if (window.location.href.indexOf('hm') === -1) {
            dfd.resolve(data);
            return dfd.promise();
          }
          var ajaxGetHistCorrIndex = getAJAX(['get_hist_corr_index'],[],['histCorrIndex'],{'category': category, 'corr_type': corr_type, 'freq': freq, 'trail': trail},10000,1);
          ajaxGetHistCorrIndex.done(function(res) {
            
              var histCorrIndex = JSON.parse(res).histCorrIndex;
              
              var dates = [];
              $.each(histCorrIndex, function(i,row) {
                  dates.push(row.pretty_date);
              });
              $.extend(true,data,{
                'hmDates': dates,
                "playState": 'pause', // 1 -> not playing, 2-> playing
                "playIndex": dates.length - 1,
                "histCorrIndex": histCorrIndex
              });
              
              drawHeatMapDates(histCorrIndex);
          });
            dfd.resolve(data);
            return dfd.promise();
        });
        
        
        $.when(d1,d2).done(function(data1,data2) {
          $.extend(true,data,data1,data2);
          setData(data);
         // var math = $('#heatmap-dates > span.highcharts-title')[0];
         // MathJax.Hub.Queue(["Typeset",MathJax.Hub,math]);

          $('#overlay').hide();
        });
              

    })();
    
    
    $('#heatmap').on('click', '#heatmap-subtitle-group > button.heatmap-subtitle', function() {

      var data = getData();
      if (data.playState == null) return;
            
      if ($(this).data('dir') === 'pause') {              
        data.playState = 'pause';
      }
      
      if ($(this).data('dir') === 'start' || $(this).data('dir') === 'end') {
        if ($(this).data('dir') === 'start' ) data.playIndex = 0;
        else data.playIndex = data.hmDates.length - 1;
        data.playState = 'pause';
      }
      
      else if ($(this).data('dir') === 'back' || $(this).data('dir') === 'forward') {
        if ($(this).data('dir') === 'back') data.playIndex = (data.playIndex >= 5 ? data.playIndex - 5 : 0);
        else data.playIndex = (data.playIndex + 5 <= data.hmDates.length - 1 ? data.playIndex + 5: data.hmDates.length);
        data.playState = $(this).data('dir');
      }
      
      
      setData(data);
      if ($(this).data('dir') !== 'pause') updateCharts($('#heatmap').highcharts(),$('#heatmap-dates').highcharts());
      return;

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

/*
    $('#corr-type-dropdown').on('click','a.corr-type', function(){
      $('#corr-type-dropdown-text').html( $(this).html() ).find('span').tooltip();
      $(this).hide()
        .siblings().show();
    });

    $('#corrselector').on('shown.bs.tooltip','[data-toggle="tooltip"]', function () {
      var tooltipID = $(this).attr('aria-describedby');
      var tooltip = $('#' + tooltipID);
      MathJax.Hub.Queue(["Typeset",MathJax.Hub,tooltip[0]]);
    });
*/

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
            var corr_type_str = (corr_types[i] === 'rho' ? 'Pearson Correlation' : (corr_types[i] === 'ktau' ? "Kendall's &#120533; Coefficient" : (corr_types[i] === 'mic' ? 'Maximal Information Coefficient (MIC)' : ''))) ;
            $('#corr_type').append('<option value="' + corr_types[i] + '" ' +  (corr_types[i] === selectedcorr_type ? 'selected' : '') + ' >' + corr_type_str + '</option>');
        }

        /*
        for (i=0;i<corr_types.length;i++) {
          var corr_type_str = getCorrName(corr_types[i]);
          if (corr_types[i] === selectedcorr_type) $('#corr-type-dropdown-text').html(corr_type_str + '&nbsp;<span href="#" data-toggle="tooltip" title="$$\tau = \sqrt{b^2} \\sum_{i} \sum_{j=1}$$" class="badge badge-primary">' + 'test' +'</span>');
          
          $('#corr-type-dropdown').append('<a class="dropdown-item corr-type" href="#" data-corr-type="' + corr_types[i] + '"  ' +  (corr_types[i] === selectedcorr_type ? 'style="display:none"' : '') + ' >' + corr_type_str + '&nbsp;<span href="#" data-toggle="tooltip" title="$$' + getCorrMath(corr_types[i]) + '$$" class="badge badge-primary">' + '[info]' +'</span>' + '</a>');
      }
      $('#corr-type-dropdown-text span[data-toggle="tooltip"], #corr-type-dropdown span[data-toggle="tooltip"]').tooltip();
    */
    }
    
    
}




function updateCharts(chartHM,chartHMDates) {
    var timeStart = new Date().getTime();

    var data = getData();
  
    if (data.hmDates === undefined || data.playIndex === undefined) return;
    var date = data.hmDates[data.playIndex];
    updatePlotLine(chartHMDates,new Date(date).getTime());
    

    var ajaxGetHistCorrel = getAJAX(
                                    ['get_hist_correl_by_date'],[],['tagsCorrel',],
                                    {"date": date, "category": data.dataInfo.category, "corr_type": data.dataInfo.corr_type, "freq": data.dataInfo.freq, "trail": data.dataInfo.trail},
                                    20000,'disabled');

    ajaxGetHistCorrel.done(function(res) {
            if (JSON.parse(res).tagsCorrel.length == null) return;
            
            var tagsCorrelDate = JSON.parse(res).tagsCorrel;
            var hm = drawHeatMap(data.tagsSeries,tagsCorrelDate,1);
            
            for (j=0;j<hm.data.length;j++) {
                chartHM.series[0].data[j].update(hm.data[j],false);
            }
            chartHM.redraw();
            $('#heatmap-subtitle-date').text(Highcharts.dateFormat('%m/%d/%Y',new Date(date).getTime()));
            
                      
            //If at end or beginning, auto-pause
            
            if (data.playIndex <= 0 ) {
              data.playState = 'pause';
              data.playIndex = 0;
            }
            
            else if (data.playIndex >= data.hmDates.length - 1) {
              data.playState = 'pause';
              data.playIndex = data.hmDates.length - 1;
            }
            
            else {
              if (data.playState === 'forward')  data.playIndex = data.playIndex + 5; //skip by week
              else data.playIndex = data.playIndex - 5; //skip by week
            }
            
            setData(data);
            
            updateHMButtons();
            var timeEnd = new Date().getTime();
            var timeWait = timeEnd-timeStart < 500 ? 500 - (timeEnd-timeStart) : 500;
          
            if (data.playState !== 'pause') setTimeout(function() { updateCharts(chartHM,chartHMDates); }, timeWait);
            
    });
}







function updateHMButtons () {
  var data = getData();
  var buttons = $('#heatmap-subtitle-group').find('button.heatmap-subtitle').removeClass('active').prop('disabled',false).end();

  if (data.playIndex === 0) {
    buttons.find('[data-dir="start"],[data-dir="back"]').prop('disabled',true);
    return;
  }
  
  if (data.playIndex === data.hmDates.length-1) {
    buttons.find('[data-dir="end"],[data-dir="forward"]').prop('disabled',true);
    return;
  }
  
  if (data.playState === 'pause') {
    buttons.find('[data-dir="pause"]').addClass('active',true);
    return;
  }

  if (data.playState === 'back') {
    buttons.find('[data-dir="back"]').addClass('active',true);
    return;
  }

  if (data.playState === 'forward') {
    buttons.find('[data-dir="forward"]').addClass('active',true);
    return;
  }  
}