$(document).ready(function() {
    
    (function() {
		
		
    })();
    
    
    $('#showLines').change(function () {
        if($(this).is(":checked")) {
            $("#minLines").removeAttr("disabled");
        }
        else {
            $("#minLines").attr("disabled", "disabled");
            $(".connectLines").remove();
        }
    });
    
    $("#submitLines").click(function(){
        $('#minLines').removeClass('is-invalid');
        //console.log('submitted');

        var x = parseFloat($("#minLines").val());

        if (x<0 || x > 1 ||$.isNumeric(x) === false) {
            $('#minLines').addClass('is-invalid');
            $( ".invalid-feedback" ).show();
            $( ".invalid-feedback" ).text('Input must be between 0 and 1');
        } else {
            $(".connectLines").remove();
            var data = $('#data').data();
            drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,x,$('#highMap').highcharts());
            drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,x,$('#highMapEurope').highcharts());
        }
       
    });
    
    
});

    

function drawMaps(tagsGFI) {
	var mapData = [];
	var gfiIndex = 0;
	var j = 0;

	for (i=0; i<tagsGFI.length; i++) {
		gfiIndex = (tagsGFI[i].code_2 === "WORLD") ? 1 : 2;
		if (tagsGFI[i]['code_' + gfiIndex].toLowerCase() === 'hk') continue;//no hk position on map
		
		mapData[j] = {}; 
		mapData[j]['hc-key']= tagsGFI[i]['code_' + gfiIndex].toLowerCase();
		mapData[j].value = tagsGFI[i].obs_end_val;
		mapData[j].name = tagsGFI[i]['name_' + gfiIndex];
		mapData[j].id = mapData[j]['hc-key'];
		mapData[j].grouping = tagsGFI[i]['grouping_' + gfiIndex];
		j++;
	}
	
	var mapDataEurope = [];
	var k = 0;
	for (i=0; i<mapData.length;i++) {
		if (mapData[i].grouping.indexOf('Europe') !== -1 ) {
			mapDataEurope[k] = mapData[i];
			k++;
		}
	}
	
	c1 = drawMap(mapData);
	c2 = drawMapEurope(mapDataEurope);

	return [c1,c2]
}

function drawMap (mapData) {        
	var o = getHighChartsMapOptions();
	$.extend(true,o,{
		chart: {
			map: 'custom/world-robinson',
			height: 640,
			marginTop: 0,
			marginRight: 0,
			marginBottom: 0,
			marginLeft: 0,
		},
		title: {
			text: 'Correlation to Global Stock Markets By Country'
		},
		series: [{
			data: mapData,
			name: 'Correlation to Global Stock Market Index',
		}]

	});
	
	var chart = Highcharts.mapChart('highMap',o);
	return chart;
}



function drawMapEurope (mapDataEurope) { 
	var o = getHighChartsMapOptions();
	$.extend(true,o,{
		chart: {
			map: 'custom/europe',
			height: 360,
			width: 460,
			marginTop: 10,
			marginRight: 120,
			marginBottom: 10,
			marginLeft: 10,
			plotBorderWidth: 1,
			plotBorderColor: 'black'
		},
		title: {
			text: ''
		},
		series: [{
			data: mapDataEurope,
		}]
	});
	
	var chart = Highcharts.mapChart('highMapEurope', o);
	$("#highMapEurope > div").addClass('float-right').css('margin-top','-60px');
	return chart;
}


	
function drawStrongCorrelations(tagsSeries,tagsCorrel,minLines,chart) {
	var points = chart.series[0].points;
	
	/*
	for (i=0;i<tagsCorrel.count;i++) {
		if ( (tagsCorrel[i].name_1 === name1 && tagsCorrel[i].name_2 === name2) || (tagsCorrel[i].name_1 === name2 && tagsCorrel[i].name_2 === name1) ) {
			console.log(tagsCorrel[i].obs_end_value);
			break;  
		}
	}*/

	
	// Puts point coordinates into tagsSeriesXY
	tagsSeriesXY = {};
	for (i=0;i<points.length;i++) {
		for (k=0;k<tagsSeries.length;k++) {
			if (tagsSeries[k].code.toLowerCase() === points[i]["hc-key"]) {
				tagsSeriesXY[points[i]["hc-key"]] = {};                    
				tagsSeriesXY[points[i]["hc-key"]].x = points[i].plotX;
				tagsSeriesXY[points[i]["hc-key"]].y = points[i].plotY;
			} //else console.log(tagsSeries[k].name + ' ' + points[i].name);
		}
	}

	//console.log(tagsSeriesXY);

	for (i=0;i<tagsCorrel.length;i++) {
		if (tagsCorrel[i].obs_end_val < minLines) continue;
		//console.log(tagsCorrel[i].obs_end_val);
		
		code1 = tagsCorrel[i].code_1.toLowerCase() ;
		code2 = tagsCorrel[i].code_2.toLowerCase() ;
		
		if (chart.userOptions.chart.map.indexOf('world') !== -1) {
			if (code1 === 'hk' || code2 === 'hk' || code1 === 'world' || code2 === 'world') continue;
		} else if (chart.userOptions.chart.map.indexOf('europe') !== -1) {
			if (tagsCorrel[i].grouping_1 == undefined || tagsCorrel[i].grouping_2 == undefined || tagsCorrel[i].grouping_1.indexOf('Europe') == -1 ||  tagsCorrel[i].grouping_2.indexOf('Europe') == -1) continue;
		}
		
		//quadratic bezier - (x,y) for (start,midpoint,end) 
		chart.renderer.path([
			
			'M'+tagsSeriesXY[code1].x+chart.plotLeft,
			tagsSeriesXY[code1].y+chart.plotTop,
			
			'Q',
			(tagsSeriesXY[code1].x+tagsSeriesXY[code2].x)/2,
			(tagsSeriesXY[code1].y + tagsSeriesXY[code2].y)/2-20,
			
			tagsSeriesXY[code2].x + chart.plotLeft,
			tagsSeriesXY[code2].y + chart.plotTop,
			])
			
		.attr({
			"stroke-width": 1,
			"zIndex": 5,
			"stroke": '#696969',
			"class": 'connectLines ' + code1 + '  ' + code2,
			"tagCorrel": JSON.stringify(tagsCorrel[i])
		})
		
		.add();
		
	}
}


function getHighChartsMapOptions (){
	var o = {
		chart: {
			backgroundColor: 'rgba(255,255,255,0)',
		},
		subtitle: {
		},
		credits: {
			enabled:false
		},
		mapNavigation: {
			enabled: false
		},
		colorAxis: {
			min: -1,
			max: 1,
			reversed: false,
			stops:[
				[0, 'rgba(0,0,255,1)'],
				[0.5, 'rgba(251,250,182,1)'],
				[1, 'rgba(255,0,0,1)']
			]
		},
		legend: {
			enabled:false,
			layout:'horizontal',
			align:'center',
			verticalAlign: 'top',
			reversed:true
		},
		plotOptions: {
			series: {
				point: {
					events: {
						mouseOver: function () {
							//console.log(this);
							code = this["hc-key"];
							//console.log('.'+ code);
							//$('.'+ code).css("stroke","blue");

							$('.'+ code).each(function(){
								var tagCorrel = JSON.parse($(this).attr('tagCorrel'));
								$(this)
									.attr('stroke',colorFormat(tagCorrel['obs_end_val']))
									.attr('stroke-width',(20*Math.pow(tagCorrel['obs_end_val'],4)).toFixed(0) + 'px')
									.attr('zIndex',6)
									.css('stroke-dasharray', '0  1000' )
									.css('animation', 'dash 5s linear')
									.css('animation-iteration-count', 'infinite')

									.addClass('selectedLines');								
							});
							
							/*
							var chart = this.series.chart;
							if (!chart.lbl) {
								chart.lbl = chart.renderer.label('')
									.attr({
										padding: 10,
										r: 10,
										fill: Highcharts.getOptions().colors[1]
									})
									.css({
										color: '#FFFFFF'
									})
									.add();
							}
							chart.lbl
								.show()
								.attr({
									text: 'x: ' + this.x + ', y: ' + this.y
								});
								*/
							
						}
					},
				},
				events: {
					mouseOut: function () {
						$('.selectedLines').removeClass('selectedLines').attr('stroke','black').attr('stroke-width',1).attr('zIndex',5);
						/*
						if (this.chart.lbl) {
							this.chart.lbl.hide();
						}
						*/
					}
				}
			}
		},/*
		tooltip: {
			useHTML: true,
			formatter: function () {
			}
		}*/
		series: [{
			states: {
				hover: {
					color: '#BADA55'
				}
			},
			dataLabels: {
				enabled: true,
				format: '{point.name}'
			},
		}]
	}	
	return o;
}


function colorFormat(v) {
	var grMap = gradient.create(
	  [-1,-0.5,-0.15,0,0.3,0.5,0.8,1],
	  ['rgba(5,0,255,1)','rgba(0,132,255,.8)','rgba(0,212,255,.6)','rgba(0,255,204,.5)','rgba(253,255,53,.4)','rgba(255,160,0,.5)','rgba(255,50,0,.8)','rgba(255,0,122,1)'],
	  'rgba'
	 );
	return gradient.valToColor(v,grMap,'rgba');				 
}
