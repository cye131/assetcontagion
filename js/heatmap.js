function drawHeatMap(tagsSeries,tagsCorrel,noChart) {
    var hm = {"data":[], "info":{} };
    var vals = [];
    var val;
    var lastDate = new Date('1970-01-01');
    
    if (tagsSeries.length === 0 || tagsCorrel.length === 0 ) return;

    var i = 0;

    for (x=0;x<tagsSeries.length;x++) {
    for (y=0;y<tagsSeries.length;y++) {
        
        hm.data[i] = {
            "s_id_x": parseInt(tagsSeries[x].s_id),
            "s_id_y": parseInt(tagsSeries[y].s_id),
            "x": x,
            "y": y,
            "color": (x === y) ? 'rgba(20,20,20,.1)' : null,
            "tooltip": false
        };
        
        $.each(tagsCorrel,function(index,tagRow) {
            if (
                (tagRow.b_id_1 !== tagsSeries[x].b_id || tagRow.b_id_2 !== tagsSeries[y].b_id) &&
                (tagRow.b_id_1 !== tagsSeries[y].b_id || tagRow.b_id_2 !== tagsSeries[x].b_id)
               ) return true;
            
            val = tagRow.h_value || tagRow.obs_end_val || null;
            val = parseFloat(val).toFixed(4);
            vals.push(val);
                        
            $.extend(true,hm.data[i],{
                "pretty_date": tagRow.h_pretty_date || tagRow.obs_end,
                "value": val,
                "obs_start": tagRow.obs_start,
                "obs_end": tagRow.obs_end,
                "freq": tagRow.freq,
                "trail": tagRow.trail,
                "last_updated": tagRow.last_updated,
                //"color": (val == null) ? '#cccccc' : colorFormat(val),
                "tooltip": (val == null) ? false : true
            });
            
            if (new Date(hm.data[i].pretty_date) > lastDate) lastDate = new Date(hm.data[i].pretty_date);
        });
        i++;
        
    }
    }
    
    var titles = [];
    $.each(tagsSeries,function(i,row) {
        titles.push(row.name);
    });
    
    $.extend(hm.info,{
        'min': Math.min(...vals),
        'max': Math.max(...vals),
        'titles': titles,
        'colorarray': {}
    });
    
    j = 0;
    var group = '';
    var usedGroups = [];
    $.each(tagsSeries,function(i,tag) {
        if ( tag.grouping == null ) return true; //skip undefined groups
        group = tag.grouping.split('.')[0];
        if ( usedGroups.indexOf(group) !== -1 ) return true;
        
        usedGroups.push(group);
        hm.info.colorarray[group] = getColorArray()[j];
        //hm.info.colorarray[j] = {"group":group,"color":getColorArray()[j]};        
        j++;
    });
    
    if (noChart) return hm;
    
    var o = getHighChartsOptions();
    $.extend(true,o, {
         chart: {
             //plotHeight = 960-marginTop-marginBottom; plotWidth = 1060-marginLeft-marginRight
             height: 1060,
             marginTop: 100,
             marginRight: 130,
             marginBottom: 200,
             marginLeft: 130,
             plotBorderWidth: 1,
             backgroundColor: null
         },
         title: {
             text: 'Correlation Matrix Between Stock Markets of Major Economies'
         },
         subtitle: {
             enabled: true,
             useHTML: true,
             style: {
                width: '100%',
                "z-index": 1
             },
             text: '<div class="row text-center"><div class="col-12 d-inline-block">'+
                        '<h4 class="text-secondary"><span class="badge badge-secondary">*Data for&nbsp;<span id="heatmap-subtitle-date">'+ Highcharts.dateFormat('%m/%d/%Y',lastDate) +'</span></span></h4>'+
                    '</div></div>'+
                    '<div class="row text-center"><div class="col-12 btn-group d-inline-block" role="group" id="heatmap-subtitle-group">' +
                        '<button class="btn btn-secondary btn-sm" type="button" disabled>Click to show changes over time&nbsp;</button>'+
                        '<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="start" style="letter-spacing:-2px">&#10074;&#9664;&#9664;</button>' +
                        '<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="back">&#9664;</button>' +
                        '<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="pause" disabled>&#10074;&#10074;</button>' +
                        '<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="forward" disabled>&#9654;</button>' +
                        '<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="end" style="letter-spacing:-2px" disabled>&#9654;&#9654;&#10074;</button>' +
                    '</div></div>'
         },
         credits: {
             enabled:false
         },
         xAxis: {
             categories: hm.info.titles,
             labels: {
                 formatter: function () {
                     var thiscat;
                     for (i=0;i<tagsSeries.length;i++) if (tagsSeries[i].name === this.value) { thiscat = tagsSeries[i].grouping.split('.')[0]; break; }
                     return '<span style="font-weight:bold;color:' + hm.info.colorarray[thiscat] + '">' + this.value  + '</span>';
                 },
                 rotation: -90,
                 y:15
             }
         },
         yAxis: {
             categories: hm.info.titles,
             title: null,
             labels: {
                 formatter: function () {
                     var thiscat;
                     for (i=0;i<tagsSeries.length;i++) if (tagsSeries[i].name === this.value) { thiscat = tagsSeries[i].grouping.split('.')[0]; break; }
                     return '<span style="font-weight:bold;color:' + hm.info.colorarray[thiscat] + '">' + this.value  + '</span>';
                 },
                 rotation: -45
             }
         },
     
         colorAxis: {
             min: -1,
             max: 1,
             reversed: false,
             stops:[
                 [0, 'rgba(5,0,255,1)'], //darkblue
                 [0.2, 'rgba(0,132,255,.8)'], //lightblue
                 [0.3, 'rgba(0,212,255,.6)'], //cyan
                 [0.5, 'rgba(179,255,179,.3)'], //green
                 [0.7, 'rgba(253,255,53,.4)'], //yellow
                 [0.8, 'rgba(255,160,0,.5)'], //orange
                 [0.9, 'rgba(255,50,0,.8)'], //red
                 [1, 'rgba(255,0,112,1)'] //violet
             ]
         },
         legend: {
             enabled:true,
             layout:'vertical',
             align:'right',
             verticalAlign: 'top',
             reversed:true,
             symbolHeight: 800,
             y:40
         },
     
         tooltip: {
             useHTML: true,
             style: {
                "z-index": 10 
             },
             formatter: function () {
                 if(this.point.tooltip === true) {
                     var cat_x=''; var cat_y=''; var proxy_x=''; var proxy_y = '';
                     for (i=0;i<tagsSeries.length;i++) {
                         if (tagsSeries[i].s_id === this.point.s_id_x) {cat_x = tagsSeries[i].grouping.split('.')[0]; proxy_x = tagsSeries[i].proxy; }
                         if (tagsSeries[i].s_id === this.point.s_id_y) {cat_y = tagsSeries[i].grouping.split('.')[0]; proxy_y = tagsSeries[i].proxy; }
                     }
                     var text =  '<table>' +
                     
                                         '<tr><td style="text-align:center;font-weight:600">' +
                                             'Trailing ' + this.point.trail + this.point.freq + ' correlation between ' +
                                             '<span style="font-weight:700;color:' +  hm.info.colorarray[cat_x] + '">' + this.series.xAxis.categories[this.point.x] + '\xB9</span>' +
                                             ' and ' +
                                             '<span style="font-weight:700;color:' +  hm.info.colorarray[cat_y] + '">' + this.series.yAxis.categories[this.point.y] + '\xB2</span>' +
                                             ' on <span style="font-weight:bold">' + this.point.pretty_date + '</span>' + 
                                             ' : ' +
                                             '<span style="font-weight:700;color:' +  (this.point.value > 0 ? 'rgba(255,0,0,1)' : 'rgba(0,0,255,1)') + '">' + (this.point.value > 0 ? '+' : '') + this.point.value + '</span>' +
                                         '</td></tr>' +
                                         '<tr style="height:1.0em"><td></td></tr>' + 
                                         '<tr><td style="text-align:right;font-size:0.7em;">' +
                                             'Series updated <span>' + this.point.last_updated + ' ET</span>' +
                                             '<br><span style="font-weight:700;color:' +  hm.info.colorarray[cat_x] + '">\xB9</span> <span style="text-align:right">Calculated using market prices of ' + proxy_x +  ' as a proxy</span>' +
                                             '<br><span style="font-weight:700;color:' +  hm.info.colorarray[cat_y] + '">\xB2</span> <span style="text-align:right">Calculated using market prices of ' + proxy_y +  ' as a proxy</span>' +
                                         '</td></tr>' +

                                     '</table>';
                     return text;
                 } else {
                     return false;
                 }
             }
         },
     
         series: [{
             name: 'Correlation',
             borderColor: 'rgba(255,255,255,0)',
             borderWidth: 1,
             type: 'heatmap',
             animation: {
                 duration: 500
            },
             data: hm.data,
             dataLabels: {
                 enabled: true,
                 formatter:  function() {
                     if (hm.info.titles.length > 10) {
                         return '';
                     } else if (this.point.tooltip === true) {
                         return (this.point.value*100).toFixed(2) + '%';
                     } else {
                         return false;
                     }
                 }
             },
             turboThreshold: 0
         }]
     });
    var chart = new Highcharts.chart('heatmap', o);

    
    var boxW =  chart.xAxis[0].width/chart.xAxis[0].categories.length;
    var boxH =  chart.yAxis[0].height/chart.yAxis[0].categories.length;
    var offsetH = chart.plotSizeY + chart.plotTop;
    var offsetW = chart.plotLeft;
        
    //Get all different categories
    j = 0;
    group = '';
    usedGroups = [];
    var groups = [];
    var groupsIndex = 0;
    $.each(tagsSeries,function(i,tag) {
        if ( tag.grouping == null ) return true; //skip undefined groups
        group = tag.grouping.split('.')[0];
        
        if ( usedGroups.indexOf(group) === -1 ) { //if not a duplicate
            usedGroups[j] = group;
            groups[j] = {
                "start": i,
                "end": i,
                "grouping": group
            };
            j++;
        } else { //if duplicate, find the index it's duplicating, and set the 'end' column there
            groupsIndex = usedGroups.indexOf(group);
            groups[groupsIndex].end = i;
        }
        
    });

    
    //Get x-y coords of labels
    //console.log(chart);
    var xticks = chart.xAxis[0].ticks;
    var yticks = chart.yAxis[0].ticks;
    /*
    console.log('x');
    console.log(xticks);
    console.log('y');
    console.log(yticks);
    */
    for (i=0;i<Object.keys(xticks).length-1;i++) {
        if (typeof groups[i] !== 'undefined') {
            //console.log(groups[i]);console.log(xticks);
            //groups[i].XAXISxstart = xticks[groups[i].start].label.attr('x') - xticks[groups[i].start].label.textPxLength/2;
            groups[i].XAXISy = xticks[groups[i].start].label.attr('y');
            //groups[i].textPxLength = ticks[i].label.textPxLength;
            //groups[i].XAXISxend = xticks[groups[i].end].label.attr('x') + xticks[groups[i].end].label.textPxLength/2;
            
            
            //groups[i].YAXISystart = yticks[groups[i].start].label.attr('y') + yticks[groups[i].start].label.textPxLength/2;
            groups[i].YAXISx = yticks[groups[i].start].label.attr('x');
            //groups[i].YAXISyend = yticks[groups[i].end].label.attr('y') - yticks[groups[i].end].label.textPxLength/2;
            
            
            //top-left, bottom-right,etc
            groups[i].yTop = offsetH-(groups[i].start)*boxH;
            groups[i].yBottom = offsetH-(groups[i].end+1)*boxH;
            groups[i].xLeft = offsetW+groups[i].start*boxW;
            groups[i].xRight = offsetW+(groups[i].end+1)*boxW;

            
            groups[i].color = hm.info.colorarray[groups[i].grouping];
            
        }
    }
    //console.log('groups');console.log(groups);
    
    //Draw x
    groups.forEach(function(group,index) {
        // horizontal line on x-axis
        chart.renderer.path(['M',group.start*boxW+offsetW+2,group.XAXISy-5,'L',(group.end+1)*boxW+offsetW-2,group.XAXISy-5])
        .attr({
            'stroke-width': 2,
            stroke: group.color
        })
        .add();
        
        // vertical line on y-axis
        chart.renderer.path(['M',group.YAXISx+5,offsetH-group.start*boxH-5,'L',group.YAXISx+5,offsetH-(group.end+1)*boxH+5])
        .attr({
            'stroke-width': 2,
            stroke: group.color
        })
        .add();
        
        
        //text and rectangles
        (function() {
            if (group.grouping === 'World') return;
            
            var text = chart.renderer.text(group.grouping,offsetW+group.start*boxW+(group.end+1-group.start)*boxW/2,group.XAXISy+100) //(text,x,y)
            .attr({
                zIndex: 5,
                'text-anchor': 'middle'
            })
            .css({
                textAlign: 'center',
                color: 'white'
            })
            .add();
            box = text.getBBox();
        
            chart.renderer.rect(box.x - 5, box.y - 5, box.width + 10, box.height + 10, 5)
            .attr({
                fill: group.color,
                stroke: 'gray',
                'stroke-width': 1,
                zIndex: 4
            })
            .add();
        })();


        //Draw a box around the square - TOP -> BOT -> LEFT -> RIGHT
        var paths = [];
        paths[0] = ['M',group.xLeft,group.yTop,'H',group.xRight]; //top-left to top-right
        paths[1] = ['M',group.xLeft,group.yBottom,'H',group.xRight]; //bottom-left to bottom-right
        paths[2] = ['M',group.xLeft,group.yBottom,'V',group.yTop]; //bottom-left to top-left
        paths[3] = ['M',group.xRight,group.yBottom,'V',group.yTop]; //bottom-right to top-right

        for (i=0;i<paths.length;i++) {
            chart.renderer.path(paths[i])
            .attr({
                'stroke-width': 2,
                "zIndex": 5,
                "stroke": group.color
            })
            .add();
        }
        
        //Region text in center of box
        fontSize = Math.round( (group.xRight-group.xLeft)/5,0 );
        if (fontSize < 6) {
            
        } else {
            if (fontSize < 8) fontSize = 8;
            if (fontSize > 20) fontSize = 20;

            chart.renderer.text(group.grouping.replace(' ','<br>'),(group.xLeft+group.xRight)/2,(group.yTop+group.yBottom)/2) 
            .attr({
                zIndex: 6,
                'text-anchor': 'middle'
            })
            .css({
                "textAlign": 'center',
                "color": group.color,
                "opacity": 0.8,
                //'text-shadow': '-0.05em 0 black, 0 0.05em black, 0.05em 0 black, 0 -0.05em ' + 'black',
                'font-size': fontSize + 'px'
            })
            .add();
        
        }
        
    });


}











/* Heat Map Date Chart
 *
 *
 *
 */

function drawHeatMapDates(histCorrIndex) {
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
                        events: {
                            click: function () {
                                var data = getData();
                                var d = new Date(this.x).toISOString().split('T')[0];
                                if (data.hmDates.indexOf(d) === -1) return;
                                
                                $.extend(true,data,{
                                    "playIndex": data.hmDates.indexOf(d),
                                    "playState": "pause"
                                });
                                setData(data);

                                updateCharts($('#heatmap').highcharts(),this.series.chart);
                            }
                        }
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
    

    var c = new Highcharts.stockChart('heatmap-dates', o);

    updatePlotLine(c,hmd[hmd.length-1].x);
}

function updatePlotLine(chart,date) {
    chart.xAxis[0].removePlotLine('plot-line');
    chart.xAxis[0].addPlotLine({
        value: date,
        color: 'rgba(255,0,0,.5)',
        width: 5,
        id: 'plot-line',
        zIndex: 3,
        label: {
            text: '<h6 class="text-danger">Currently displayed date</h6>',
            align: 'left',
            verticalAlign: 'top',
            rotation: 90,
            useHTML: true
        }
    });
}






/* Color Formatter
 *
 *
 *
 */

/*
function colorFormat(v) {
    var maxColor;
    var minColor;
    var rgba = [];
    if (v <= 0) maxColor = [0,0,255,1];
    else maxColor = [255,0,0,1];
    minColor = [251,250,182,0];
    
    for(i=0;i<=2;i++) {
      rgba[i] = parseInt(((maxColor[i] - minColor[i]) * Math.abs(v)).toFixed(0)) + minColor[i];
    }
    rgba[3] = parseFloat(((maxColor[3] - minColor[3]) * Math.abs(v)).toFixed(2)) + minColor[3];
    
    
    return 'rgba(' + rgba.join(',') + ')';
}
*/