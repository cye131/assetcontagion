$(document).ready(function() {
    
    

    makeHeatMap(heatMapData,tags);
    drawHeatMapUnderlines(heatMapData,tags);

    function makeHeatMap(heatMapData) {        
        //Convert obj to array
        var data = Object.keys(heatMapData.data).map(function(e) {
          return heatMapData.data[e];
        });

        console.log(heatMapData);
        
        var max = heatMapData.info.max;
        var min = heatMapData.info.min;
        var target = 0;
        
        colorstops = [];
        zeroStop = (target - min) / (max - min);
    
        if (min >= 0) {
            colorstops[0] = [];
            colorstops[0][0] = 0;
            colorstops[0][1] = 'rgba(0,0,0,0)';
            colorstops[1] = [];
            colorstops[1][0] = 1;
            colorstops[1][1] = 'rgba(255,0,0,0.7)';
        } else {
            colorstops[0] = [];
            colorstops[0][0] = zeroStop;
            colorstops[0][1] = '#4d94ff';
            colorstops[1] = [];
            colorstops[1][0] = 0;
            colorstops[1][1] = '#ffffff';
            colorstops[2] = [];
            colorstops[2][0] = 1;
            colorstops[2][1] = '#E59A9A';
        }
    
    console.log(colorstops);
    
        Highcharts.chart('heatmap', {
            chart: {
                type: 'heatmap',
                height: 1060,
                marginTop: 40,
                marginRight: 40,
                marginBottom: 200,
                marginLeft: 200,
                plotBorderWidth: 1,
                backgroundColor: null
            },
            title: {
                text: 'Correlation Matrix'
            },
            credits: {
                enabled:false
            },
            xAxis: {
                categories: heatMapData.info.titles,
                labels: {
                    formatter: function () {
                        var thisclass;
                        for (i=0;i<tags.length;i++) {
                            if (tags[i].title === this.value) {
                                thisclass = tags[i].class;
                                break;
                            }
                        }
                        return '<span style="font-weight:bold;color:' + heatMapData.info.colorarray[thisclass] + '">' + this.value  + '</span>';
                    }
                }
            },
            yAxis: {
                categories: heatMapData.info.titles,
                title: null,
                labels: {
                    formatter: function () {
                        var thisclass;
                        for (i=0;i<tags.length;i++) {
                            if (tags[i].title === this.value) {
                                thisclass = tags[i].class;
                                break;
                            }
                        }
                        return '<span style="font-weight:bold;color:' + heatMapData.info.colorarray[thisclass] + '">' + this.value  + '</span>';
                    },
                    rotation: -45
                }
            },
        
            colorAxis: {
                min: 0,
                max: 1,
                stops: colorstops
            },
        
            legend: {
                enabled:false
            },
        
            tooltip: {
                formatter: function () {
                    if(this.point.tooltip === true) {
                        return 'Current 30-day correlation between <b>' + this.series.xAxis.categories[this.point.x] + '</b> and <b>' + this.series.yAxis.categories[this.point.y] + '</b>: <b>' + this.point.value + '<b><br>Current as of <b>' +this.point.obs_end + '<b>';
                    } else {
                        return false;
                    }
                }
            },
        
            series: [{
                name: 'Correlation',
                borderWidth: 1,
                data: data,
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
                }
            }]
        });
        
    }
    
    function drawHeatMapUnderlines (heatMapData,tags) {
        var chart=$("#heatmap").highcharts();
        var secwidth =  chart.xAxis[0].width/chart.xAxis[0].categories.length;
        var origin = chart.series[0].points[0];
        var squareH = origin.shapeArgs.height;
        var squareW = origin.shapeArgs.width;
        var offsetH = chart.plotSizeY + chart.plotTop;
        var offsetW = chart.plotLeft /*+ chart.margin[3]*/;
        
        console.log(offsetW);
        console.log(squareW);
        
        //Get all different categories
        var tagskeys = Object.keys(tags);
        var classes = [];
        var groups = [];
        var j = 0;
        
        for (i=0;i<tagskeys.length;i++){
            if (classes.indexOf(tags[tagskeys[i]].class) <= -1) { // if not a duplicate
                //console.log("Not duplicate");console.log(i);console.log( tags[tagskeys[i]].class);
                classes[j] = tags[tagskeys[i]].class;
                groups[j] = [];
                groups[j].start = i;
                groups[j].end = i;
                groups[j].class = tags[tagskeys[i]].class;
                j++;
            } else { //if duplicate, find the index it's duplicating, and set the 'end' column there
                //console.log("Duplicate");console.log(classes);console.log(tags[tagskeys[i]].class);classes.indexOf(tags[tagskeys[i]].class);
                index = classes.indexOf(tags[tagskeys[i]].class);
                groups[index].end = i;
            }
        }
        console.log(groups);
        
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
                groups[i].XAXISxstart = xticks[groups[i].start].label.attr('x') - xticks[groups[i].start].label.textPxLength/2;
                groups[i].XAXISy = xticks[groups[i].start].label.attr('y');
                //groups[i].textPxLength = ticks[i].label.textPxLength;
                groups[i].XAXISxend = xticks[groups[i].end].label.attr('x') + xticks[groups[i].end].label.textPxLength/2;
                
                
                groups[i].YAXISystart = yticks[groups[i].start].label.attr('y') + yticks[groups[i].start].label.textPxLength/2;
                groups[i].YAXISx = yticks[groups[i].start].label.attr('x');
                groups[i].YAXISyend = yticks[groups[i].end].label.attr('y') - yticks[groups[i].end].label.textPxLength/2;

                
                groups[i].color = heatMapData.info.colorarray[groups[i].class];
                
            }
        }
        console.log('groups');console.log(groups);
        
        //Draw x
        groups.forEach((group,index) => {
            chart.renderer.path(['M',group.start*squareW+offsetW+5,group.XAXISy+10,'L',(group.end+1)*squareW+offsetW-5,group.XAXISy+10])
            .attr({
                'stroke-width': 2,
                stroke: group.color
            })
            .add();

            /*
            chart.renderer.path(['M',group.XAXISxstart,group.XAXISy+10,'L',group.XAXISxend,group.XAXISy+10])
            .attr({
                'stroke-width': 2,
                stroke: group.color
            })
            .add();
            */
            var text = chart.renderer.text(group.class,(group.XAXISxstart+group.XAXISxend)/2,group.XAXISy+40) //(text,x,y)
            .attr({
                zIndex: 5,
                'text-anchor': 'middle'
            })
            .css({
                textAlign: 'center',
                color: 'white'
            })
            .add();
            console.log(text);    
            box = text.getBBox();
        
            chart.renderer.rect(box.x - 5, box.y - 5, box.width + 10, box.height + 10, 5)
            .attr({
                fill: group.color,
                stroke: 'gray',
                'stroke-width': 1,
                zIndex: 4
            })
            .add();

            
            //draw Y
            chart.renderer.path(['M',group.YAXISx+5,offsetH-group.start*squareH-5,'L',group.YAXISx+5,offsetH-(group.end+1)*squareH+5])
            .attr({
                'stroke-width': 2,
                stroke: group.color
            })
            .add();
            
            
        });
        
        /*
        chart.renderer.path(['M', 228, 479,'L', 412, 479])//M 75 223.5 L 593 223.5
            .attr({
                'stroke-width': 2,
                stroke: 'red'
            })
            .add();
   */
    }



});