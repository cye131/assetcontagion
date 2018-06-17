$(document).ready(function() {
    //Obj to Array
    Object.keys(heatMapData.data).map(function(e) {
          return heatMapData.data[e];
    });

    
    function startSpinner() {
        $('#spinnercontainer').show();
        $('.sk-circle').show();
        $(".overlay").show();
        //$('#resultscontainer').hide();
    }
    
    function endSpinner() {
        $('#spinnercontainer').hide();
        $('.sk-circle').hide();
        $(".overlay").hide();
       // $('#resultscontainer').show();
    }

    function editSpinner(str) {
        $('#loadmessage').text(str);
    }

    makeHeatMap(heatMapData,tagsSeries);
    drawHeatMapUnderlines(heatMapData,tagsSeries);
    endSpinner();

    window.playState = 0; // 0 -> never played, 1 -> paused, 2-> playing
    window.playIndex = 0;
    window.dateCount = [];
    

    $("#playHistorical").click(function(){
        if (window.playState === 0) {
            startSpinner();
            editSpinner('Loading historical data...');
            
            window.playState = 2;
            
            var lastDate = new Date(getLastDate());
            lastDate.setDate(lastDate.getDate() - 1);
            lastDate = lastDate.toISOString().split('T')[0];
            getDates(lastDate);
            
            endSpinner();
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
    
        

    function getLastDate() {
        dates = [];
        for (i=0;i<tagsCorrel.length;i++) {
            dates[i] = parseFloat( tagsCorrel[i].obs_end.replace('-','').replace('-','') );
        }
        date = Math.min.apply(Math,dates).toString();
        return date.slice(0,4) + '-' + date.slice(4,6) + '-' + date.slice(6,8);
    }
    
    function getDates (lastDate) {        
        var model = []; var logic=[];
        model[0] = 'get_date_count_by_hist_correl';
        toScript = ['dateCount'];
        $.ajax({
            url: 'routerAjax.php',
            type: 'POST',
            data: {
                model: model,
                logic: logic,
                toScript: toScript,
                date: lastDate
                },
            dataType: 'html',
            cache: false,
            timeout: 10000,
            success: function(res){
                if (isJson(res)) {
                    res = JSON.parse(res);
                    console.log(res);
                    window.dateCount = res.dateCount;
                    getHistCorrel(window.dateCount,0);
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
    
    function getHistCorrel(dateCount,i) {
        window.playIndex = i;
        if (window.dateCount == undefined || window.playIndex == undefined) return;
        if (window.playState !== 2) {
            return;
        }
                
        var model = []; var logic=[];
        model[0] = 'get_tags_series';
        model[1] = 'get_hist_correl_by_date';
        logic[0] = 'heatmap';
       
        var date = dateCount[i].pretty_date;
        console.log(date);
        
        $.ajax({
            url: 'routerAjax.php',
            type: 'POST',
            data: {
                model: model,
                logic: logic,
                toScript:  ['tagsSeries','tagsCorrel','heatMapData'],
                fromAjax: {date: date, category: 'reg'}
                },
            dataType: 'html',
            cache: false,
            timeout: 10000,
            success: function(res){
                //console.log(res);
                res = JSON.parse(res);
                chart = $("#heatmap").highcharts();
                //console.log(chart.series[0].data);
                console.log(res.heatMapData.data);
                
                for (j=0;j<res.heatMapData.data.length;j++) {
                    chart.series[0].data[j].update({
                        value: res.heatMapData.data[j].value,
                        color: res.heatMapData.data[j].color,
                        pretty_date: res.heatMapData.data[j].pretty_date,
                        tooltip: res.heatMapData.data[j].tooltip
                    },false);
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
                
                /*
                chart.destroy();
                makeHeatMap(res.heatMapData,res.tagsSeries);
                drawHeatMapUnderlines(res.heatMapData,res.tagsSeries);
                */
                if (window.playIndex < dateCount.length-1) {
                    window.playIndex++;
                    setTimeout(function() { getHistCorrel(dateCount,window.playIndex); }, 500);
                } else {
                    window.playIndex = 0;
                }
                /*chart.series[0].update({
                    //pointStart: res.heatMapData.data.pointStart,
                    data: res.heatMapData.data
                }, true);*/
                //makeHeatMap(res.heatMapData,res.tagsSeries);
                //data = JSON.parse(data);
            },
            error:function(){
                validateFail('Historical data not found.');
            }
        });
        
    }

    
    function makeHeatMap(heatMapData,tagsSeries) {        
        
       Highcharts.chart('heatmap', {
            chart: {
                //plotHeight = 960-marginTop-marginBottom; plotWidth = 1060-marginLeft-marginRight
                type: 'heatmap',
                height: 1060,
                marginTop: 80,
                marginRight: 120,
                marginBottom: 200,
                marginLeft: 120,
                plotBorderWidth: 1,
                backgroundColor: null
            },
            title: {
                useHTML: true,
                text: 'Correlation Matrix Between Stock Markets of Major Economies'
            },
            subtitle: {
                enabled: true,
                useHTML: true,
                text: '<button class="btn btn-primary btn-sm" type="button" id="playHistorical">&#9654; Click here to show changes over time!</button>'
            },
            credits: {
                enabled:false
            },
            xAxis: {
                categories: heatMapData.info.titles,
                labels: {
                    formatter: function () {
                        var thiscat;
                        for (i=0;i<tagsSeries.length;i++) if (tagsSeries[i].name === this.value) { thiscat = tagsSeries[i].grouping_first_part; break; }
                        return '<span style="font-weight:bold;color:' + heatMapData.info.colorarray[thiscat] + '">' + this.value  + '</span>';
                    },
                    rotation: -90,
                    y:15
                }
            },
            yAxis: {
                categories: heatMapData.info.titles,
                title: null,
                labels: {
                    formatter: function () {
                        var thiscat;
                        for (i=0;i<tagsSeries.length;i++) if (tagsSeries[i].name === this.value) { thiscat = tagsSeries[i].grouping_first_part; break; }
                        return '<span style="font-weight:bold;color:' + heatMapData.info.colorarray[thiscat] + '">' + this.value  + '</span>';
                    },
                    rotation: -45
                }
            },
        
            colorAxis: {
                min: -1,
                max: 1,
                reversed: false,
                stops:[
                    [0, 'rgba(0,0,255,1)'],
                    [0.5, 'rgba(251,250,182,0)'],
                    [1, 'rgba(255,0,0,1)']
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
                formatter: function () {
                    if(this.point.tooltip === true) {
                        var cat_x=''; var cat_y='';
                        for (i=0;i<tagsSeries.length;i++) {
                            if (tagsSeries[i].s_id === this.point.s_id_x) {cat_x = tagsSeries[i].grouping_first_part; proxy_x = tagsSeries[i].proxy; }
                            if (tagsSeries[i].s_id === this.point.s_id_y) {cat_y = tagsSeries[i].grouping_first_part; proxy_y = tagsSeries[i].proxy; }
                        }
                        var text =  '<table>' +
                        
                                            '<tr><td style="text-align:center;font-weight:600">' +
                                                'Trailing ' + this.point.trail + this.point.freq + ' correlation between ' +
                                                '<span style="font-weight:700;color:' +  heatMapData.info.colorarray[cat_x] + '">' + this.series.xAxis.categories[this.point.x] + '\xB9</span>' +
                                                ' and ' +
                                                '<span style="font-weight:700;color:' +  heatMapData.info.colorarray[cat_y] + '">' + this.series.yAxis.categories[this.point.y] + '\xB2</span>' +
                                                ' on <span style="font-weight:bold">' + this.point.pretty_date + '</span>' + 
                                                ' : ' +
                                                '<span style="font-weight:700;color:' +  (this.point.value > 0 ? 'rgba(255,0,0,1)' : 'rgba(0,0,255,1)') + '">' + (this.point.value > 0 ? '+' : '') + this.point.value + '</span>' +
                                            '</td></tr>' +
                                            '<tr style="height:1.0em"><td></td></tr>' + 
                                            '<tr><td style="text-align:right;font-size:0.7em;">' +
                                                'Series updated <span>' + this.point.last_updated + ' ET</span>' +
                                                '<br><span style="font-weight:700;color:' +  heatMapData.info.colorarray[cat_x] + '">\xB9</span> <span style="text-align:right">Calculated using market prices of ' + proxy_x +  ' as a proxy</span>' +
                                                '<br><span style="font-weight:700;color:' +  heatMapData.info.colorarray[cat_y] + '">\xB2</span> <span style="text-align:right">Calculated using market prices of ' + proxy_y +  ' as a proxy</span>' +
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
                animation: {
                    duration: 500
            },   

                data: heatMapData.data,
                dataLabels: {
                    enabled: true,
                    formatter:  function() {
                        if (heatMapData.info.titles.length > 10) {
                            return '';
                        } else if (this.point.tooltip === true) {
                            return (this.point.value*100).toFixed(2) + '%';
                        } else {
                            return false;
                        }
                    }
                },
                turboThreshold: 0 // #3404, remove after 4.0.5 release
            }]
        });
        
    }
    
    function drawHeatMapUnderlines (heatMapData,tagsSeries) {
        var chart=$("#heatmap").highcharts();
        var boxW =  chart.xAxis[0].width/chart.xAxis[0].categories.length;
        var boxH =  chart.yAxis[0].height/chart.yAxis[0].categories.length;

        //var origin = chart.series[0].points[0];
       /* var squareH = origin.shapeArgs.height;
        var squareW = origin.shapeArgs.width; */
        var offsetH = chart.plotSizeY + chart.plotTop;
        var offsetW = chart.plotLeft /*+ chart.margin[3]*/;
        
        console.log(offsetW);
        console.log(boxW);
        
        //Get all different categories
        var tagskeys = Object.keys(tagsSeries);
        var categories = [];
        var groups = [];
        var j = 0;
        
        for (i=0;i<tagskeys.length;i++){
            var groupingFirstPart = tagsSeries[tagskeys[i]].grouping_first_part;
            if (categories.indexOf( groupingFirstPart ) <= -1) { // if not a duplicate
                //console.log("Not duplicate");console.log(i);console.log( tags[tagskeys[i]].class);
                categories[j] =  groupingFirstPart ;
                groups[j] = [];
                groups[j].start = i;
                groups[j].end = i;
                groups[j].grouping =  groupingFirstPart ;
                j++;
            } else { //if duplicate, find the index it's duplicating, and set the 'end' column there
                //console.log("Duplicate");console.log(classes);console.log(tags[tagskeys[i]].class);classes.indexOf(tags[tagskeys[i]].class);
                index = categories.indexOf( groupingFirstPart );
                groups[index].end = i;
            }
        }
        //console.log(groups);
        
        //Get x-y coords of labels
        console.log(chart);
        var xticks = chart.xAxis[0].ticks;
        var yticks = chart.yAxis[0].ticks;
        
        console.log('x');
        console.log(xticks);
        console.log('y');
        console.log(yticks);
        //console.log(Object.keys(ticks).length);
        colorindex = 0;
        
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

                
                groups[i].color = heatMapData.info.colorarray[groups[i].grouping];
                
            }
        }
        console.log('groups');console.log(groups);
        
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
                    zIndex: 5,
                    stroke: group.color
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
                    textAlign: 'center',
                    color: group.color,
                    opacity: '0.4',
                    //'text-shadow': '-0.05em 0 black, 0 0.05em black, 0.05em 0 black, 0 -0.05em ' + 'black',
                    'font-size': fontSize + 'px'
                })
                .add();
            
            }
            
        });
    }

});

function isJson(item) {
    item = typeof item !== "string"
        ? JSON.stringify(item)
        : item;

    try {
        item = JSON.parse(item);
    } catch (e) {
        return false;
    }

    if (typeof item === "object" && item !== null) {
        return true;
    }

    return false;
}
