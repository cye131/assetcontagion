$(document).ready(function(){(function(){var pathname=window.location.pathname;var dd=!1;var navbar=$('#navbar');navbar.find('a.dropdown-item').each(function(i,el){if($(el).attr("href")===pathname){$(el).addClass('active');$(el).closest('li.nav-item').addClass('active');dd=!0;return!1}});if(dd===!0)return;navbar.find('li.nav-item').each(function(i,el){var a=$(el).find('a:first');if(a.attr("href")===pathname){$(el).addClass('active');return!1}})})()});function getData(){return $('#data').data()}
function setData(d){$('#data').data(d)}
function isJson(item){item=typeof item!=="string"?JSON.stringify(item):item;try{item=JSON.parse(item)}catch(e){return!1}
if(typeof item==="object"&&item!==null){return!0}
return!1}
function getAJAX(model,logic,toScript,fromAjax,timeout,disableOverlay){var timerStart=Date.now();if(!disableOverlay)$('#overlay').show();return $.ajax({url:'routerAjax.php',type:'POST',data:{model:model,logic:logic,toScript:toScript,fromAjax:fromAjax},dataType:'html',cache:!1,timeout:timeout}).fail(function(res){console.log(res);console.log('AJAX Error')}).always(function(res){if(!disableOverlay)$('#overlay').hide();console.log('AJAX Time: '+(Date.now()-timerStart))})}
function getColorArray(){return['#4572A7','#AA4643','#0ba828','#80699B','#3D96AE','#DB843D','#92A8CD','#A47D7C','#B5CA92',"#7cb5ec","#434348","#90ed7d","#f7a35c","#8085e9","#f15c80","#e4d354","#2b908f","#f45b5b","#91e8e1"]}
function getDataTablesOptions(){var o={iDisplayLength:15,dom:"<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>"+"<'row'<'col-sm-12 px-0'tr>>"+"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",buttons:['copy','csv','excel',],language:{search:"Search a lag value:",info:"_START_ - _END_ (_TOTAL_ total rows)"},columnDefs:[{}],order:[0,"asc"],pagingType:"full_numbers"};return o}
function getHighChartsOptions(){var o={chart:{style:{fontFamily:'inherit'},},title:{style:{fontFamily:'inherit'}}};return o}
function arrayColumn(array,columnName){return array.map(function(value,index){return value[columnName]})}
function getCorrName(corr_type){if(corr_type==='rho')return"Pearson's Correlation Coefficient";else if(corr_type==='ktau')return"Kendall's &#120591; Coefficient";else if(corr_type==='mic')return"Maximal Information Coefficient";else if(corr_type==='srho')return"Spearman's Rho"}
function getCorrMath(corr_type){if(corr_type==='rho')return"Pearson's Correlation Coefficient";else if(corr_type==='ktau')return" \\tau = \\frac{2}{n(n-1)} \\sum_{i=1}^{N} \\sum_{j=i}^{N} \\begin{cases} 1, & \\text{if } (x_i > x_j \\text{ and } y_j > y_j)  \\text{ or } (x_i < x_j \\text{ and } y_j < y_j)  \\\\0, &\\text{if } x_i = x_j \\text{ or } y_i = y_j  \\\\-1, &\\text{otherwise} \\end{cases}";else if(corr_type==='mic')return"Maximal Information Coefficient";else if(corr_type==='srho')return"Spearman's Rho"};$(document).ready(function(){(function(){var category='reg';var corr_type=sessionStorage.getItem('corr_type')||'rho';var freq=sessionStorage.getItem('freq')||'d';var trail=sessionStorage.getItem('trail')||30;var data={};$('#overlay').show();var d1=$.Deferred(function(dfd){var ajaxGetSpecsCategories=getAJAX(['get_specs_categories'],[],['specsCategories'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);var ajaxGetTagsSeries=getAJAX(['get_tags_series'],[],['tagsSeries'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);var ajaxGetTagsCorrel=getAJAX(['get_tags_correl'],[],['tagsCorrel'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);$.when(ajaxGetSpecsCategories,ajaxGetTagsSeries,ajaxGetTagsCorrel).done(function(r1,r2,r3){$.extend(!0,data,{dataInfo:{"category":category,"corr_type":corr_type,"freq":freq,"trail":trail},specsCategories:JSON.parse(r1[0]).specsCategories,tagsSeries:JSON.parse(r2[0]).tagsSeries,tagsCorrel:JSON.parse(r3[0]).tagsCorrel,tagsGFI:[]});$.each(data.tagsCorrel,function(i,row){if(row.grouping_1==='World.'||row.grouping_2==='World.')data.tagsGFI.push(row)});setCategoryOptions(data);if(window.location.href.indexOf('hm')!==-1){drawHeatMap(data.tagsSeries,data.tagsCorrel)}else if(window.location.href.indexOf('map')!==-1){var charts=drawMaps(data.tagsGFI);drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,0.75,charts[0]);drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,0.75,charts[1])}
dfd.resolve(data);return dfd.promise()})});var d2=$.Deferred(function(dfd){if(window.location.href.indexOf('hm')===-1){dfd.resolve(data);return dfd.promise()}
var ajaxGetHistCorrIndex=getAJAX(['get_hist_corr_index'],[],['histCorrIndex'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);ajaxGetHistCorrIndex.done(function(res){var histCorrIndex=JSON.parse(res).histCorrIndex;var dates=[];$.each(histCorrIndex,function(i,row){dates.push(row.pretty_date)});$.extend(!0,data,{'hmDates':dates,"playState":'pause',"playIndex":dates.length-1,"histCorrIndex":histCorrIndex});drawHeatMapDates(histCorrIndex)});dfd.resolve(data);return dfd.promise()});$.when(d1,d2).done(function(data1,data2){$.extend(!0,data,data1,data2);setData(data);$('#overlay').hide()})})();$('#heatmap').on('click','#heatmap-subtitle-group > button.heatmap-subtitle',function(){var data=getData();if(data.playState==null)return;if($(this).data('dir')==='pause'){data.playState='pause'}
if($(this).data('dir')==='start'||$(this).data('dir')==='end'){if($(this).data('dir')==='start')data.playIndex=0;else data.playIndex=data.hmDates.length-1;data.playState='pause'}else if($(this).data('dir')==='back'||$(this).data('dir')==='forward'){if($(this).data('dir')==='back')data.playIndex=(data.playIndex>=5?data.playIndex-5:0);else data.playIndex=(data.playIndex+5<=data.hmDates.length-1?data.playIndex+5:data.hmDates.length);data.playState=$(this).data('dir')}
setData(data);if($(this).data('dir')!=='pause')updateCharts($('#heatmap').highcharts(),$('#heatmap-dates').highcharts());return});$('a[data-toggle="tab"]').on('shown.bs.tab',function(){if($('.successmsg').text().length>0){$('.chart').each(function(){$(this).highcharts().reflow()})}});$("#corrselector").submit(function(e){e.preventDefault();var freqtrail=$('#freqtrail').val();freqtrail=freqtrail.split('.');$('#freq').val(freqtrail[0]);$('#trail').val(freqtrail[1]);window.sessionStorage.setItem("freq",freqtrail[0]);window.sessionStorage.setItem("trail",freqtrail[1]);window.sessionStorage.setItem("corr_type",$('#corr_type').val());$("#corrselector")[0].submit()})});function setCategoryOptions(data){if(data.specsCategories.length!==1)console.log('ERR: No category selected');else{var freqtrails=data.specsCategories[0].cat_freqtrails.split(',');var selectedfreqtrail=(typeof sessionStorage.getItem('freq')!==undefined&&typeof sessionStorage.getItem('trail')!==undefined)?sessionStorage.getItem('freq')+'.'+sessionStorage.getItem('trail'):null;for(i=0;i<freqtrails.length;i++){var freqtrails_split=freqtrails[i].split('.');var freqtrails_str=freqtrails_split[1]+(freqtrails_split[0]==='d'?'-day':freqtrails_split[0])+' '+(freqtrails_split[0]==='d'?'Rolling Correlation':'Correlation');$('#freqtrail').append('<option value="'+freqtrails[i]+'" '+(freqtrails[i]===selectedfreqtrail?'selected':'')+' >'+freqtrails_str+'</option>')}
var corr_types=data.specsCategories[0].cat_corrtypes.split(',');var selectedcorr_type=(typeof sessionStorage.getItem('corr_type')!==undefined)?sessionStorage.getItem('corr_type'):null;for(i=0;i<corr_types.length;i++){var corr_type_str=(corr_types[i]==='rho'?'Pearson Correlation':(corr_types[i]==='ktau'?"Kendall's &#120533; Coefficient":(corr_types[i]==='mic'?'Maximal Information Coefficient (MIC)':'')));$('#corr_type').append('<option value="'+corr_types[i]+'" '+(corr_types[i]===selectedcorr_type?'selected':'')+' >'+corr_type_str+'</option>')}}}
function updateCharts(chartHM,chartHMDates){var timeStart=new Date().getTime();var data=getData();if(data.hmDates===undefined||data.playIndex===undefined)return;var date=data.hmDates[data.playIndex];updatePlotLine(chartHMDates,new Date(date).getTime());var ajaxGetHistCorrel=getAJAX(['get_hist_correl_by_date'],[],['tagsCorrel',],{"date":date,"category":data.dataInfo.category,"corr_type":data.dataInfo.corr_type,"freq":data.dataInfo.freq,"trail":data.dataInfo.trail},20000,'disabled');ajaxGetHistCorrel.done(function(res){if(JSON.parse(res).tagsCorrel.length==null)return;var tagsCorrelDate=JSON.parse(res).tagsCorrel;var hm=drawHeatMap(data.tagsSeries,tagsCorrelDate,1);for(j=0;j<hm.data.length;j++){chartHM.series[0].data[j].update(hm.data[j],!1)}
chartHM.redraw();$('#heatmap-subtitle-date').text(Highcharts.dateFormat('%m/%d/%Y',new Date(date).getTime()));if(data.playIndex<=0){data.playState='pause';data.playIndex=0}else if(data.playIndex>=data.hmDates.length-1){data.playState='pause';data.playIndex=data.hmDates.length-1}else{if(data.playState==='forward')data.playIndex=data.playIndex+5;else data.playIndex=data.playIndex-5}
setData(data);updateHMButtons();var timeEnd=new Date().getTime();var timeWait=timeEnd-timeStart<500?500-(timeEnd-timeStart):500;if(data.playState!=='pause')setTimeout(function(){updateCharts(chartHM,chartHMDates)},timeWait)})}
function updateHMButtons(){var data=getData();var buttons=$('#heatmap-subtitle-group').find('button.heatmap-subtitle').removeClass('active').prop('disabled',!1).end();if(data.playIndex===0){buttons.find('[data-dir="start"],[data-dir="back"]').prop('disabled',!0);return}
if(data.playIndex===data.hmDates.length-1){buttons.find('[data-dir="end"],[data-dir="forward"]').prop('disabled',!0);return}
if(data.playState==='pause'){buttons.find('[data-dir="pause"]').addClass('active',!0);return}
if(data.playState==='back'){buttons.find('[data-dir="back"]').addClass('active',!0);return}
if(data.playState==='forward'){buttons.find('[data-dir="forward"]').addClass('active',!0);return}};$(document).ready(function(){(function(){})();$('#showLines').change(function(){if($(this).is(":checked")){$("#minLines").removeAttr("disabled")}else{$("#minLines").attr("disabled","disabled");$(".connectLines").remove()}});$("#submitLines").click(function(){$('#minLines').removeClass('is-invalid');var x=parseFloat($("#minLines").val());if(x<0||x>1||$.isNumeric(x)===!1){$('#minLines').addClass('is-invalid');$(".invalid-feedback").show();$(".invalid-feedback").text('Input must be between 0 and 1')}else{$(".connectLines").remove();var data=$('#data').data();drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,x,$('#highMap').highcharts());drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,x,$('#highMapEurope').highcharts())}})});function drawMaps(tagsGFI){var mapData=[];var gfiIndex=0;var j=0;for(i=0;i<tagsGFI.length;i++){gfiIndex=(tagsGFI[i].code_2==="WORLD")?1:2;if(tagsGFI[i]['code_'+gfiIndex].toLowerCase()==='hk')continue;mapData[j]={};mapData[j]['hc-key']=tagsGFI[i]['code_'+gfiIndex].toLowerCase();mapData[j].value=tagsGFI[i].obs_end_val;mapData[j].name=tagsGFI[i]['name_'+gfiIndex];mapData[j].id=mapData[j]['hc-key'];mapData[j].grouping=tagsGFI[i]['grouping_'+gfiIndex];j++}
var mapDataEurope=[];var k=0;for(i=0;i<mapData.length;i++){if(mapData[i].grouping.indexOf('Europe')!==-1){mapDataEurope[k]=mapData[i];k++}}
c1=drawMap(mapData);c2=drawMapEurope(mapDataEurope);return[c1,c2]}
function drawMap(mapData){var o=getHighChartsMapOptions();$.extend(!0,o,{chart:{map:'custom/world-robinson',height:640,marginTop:0,marginRight:0,marginBottom:0,marginLeft:0,},title:{text:'Correlation to Global Stock Markets By Country'},series:[{data:mapData,name:'Correlation to Global Stock Market Index',}]});var chart=Highcharts.mapChart('highMap',o);return chart}
function drawMapEurope(mapDataEurope){var o=getHighChartsMapOptions();$.extend(!0,o,{chart:{map:'custom/europe',height:360,width:460,marginTop:10,marginRight:120,marginBottom:10,marginLeft:10,plotBorderWidth:1,plotBorderColor:'black'},title:{text:''},series:[{data:mapDataEurope,}]});var chart=Highcharts.mapChart('highMapEurope',o);$("#highMapEurope > div").addClass('float-right').css('margin-top','-60px');return chart}
function drawStrongCorrelations(tagsSeries,tagsCorrel,minLines,chart){var points=chart.series[0].points;tagsSeriesXY={};for(i=0;i<points.length;i++){for(k=0;k<tagsSeries.length;k++){if(tagsSeries[k].code.toLowerCase()===points[i]["hc-key"]){tagsSeriesXY[points[i]["hc-key"]]={};tagsSeriesXY[points[i]["hc-key"]].x=points[i].plotX;tagsSeriesXY[points[i]["hc-key"]].y=points[i].plotY}}}
for(i=0;i<tagsCorrel.length;i++){if(tagsCorrel[i].obs_end_val<minLines)continue;code1=tagsCorrel[i].code_1.toLowerCase();code2=tagsCorrel[i].code_2.toLowerCase();if(chart.userOptions.chart.map.indexOf('world')!==-1){if(code1==='hk'||code2==='hk'||code1==='world'||code2==='world')continue}else if(chart.userOptions.chart.map.indexOf('europe')!==-1){if(tagsCorrel[i].grouping_1==undefined||tagsCorrel[i].grouping_2==undefined||tagsCorrel[i].grouping_1.indexOf('Europe')==-1||tagsCorrel[i].grouping_2.indexOf('Europe')==-1)continue}
chart.renderer.path(['M'+tagsSeriesXY[code1].x+chart.plotLeft,tagsSeriesXY[code1].y+chart.plotTop,'Q',(tagsSeriesXY[code1].x+tagsSeriesXY[code2].x)/2,(tagsSeriesXY[code1].y+tagsSeriesXY[code2].y)/2-20,tagsSeriesXY[code2].x+chart.plotLeft,tagsSeriesXY[code2].y+chart.plotTop,]).attr({"stroke-width":1,"zIndex":5,"stroke":'#696969',"class":'connectLines '+code1+'  '+code2,"tagCorrel":JSON.stringify(tagsCorrel[i])}).add()}}
function getHighChartsMapOptions(){var o={chart:{backgroundColor:'rgba(255,255,255,0)',},subtitle:{},credits:{enabled:!1},mapNavigation:{enabled:!1},colorAxis:{min:-1,max:1,reversed:!1,stops:[[0,'rgba(0,0,255,1)'],[0.5,'rgba(251,250,182,1)'],[1,'rgba(255,0,0,1)']]},legend:{enabled:!1,layout:'horizontal',align:'center',verticalAlign:'top',reversed:!0},plotOptions:{series:{point:{events:{mouseOver:function(){code=this["hc-key"];$('.'+code).each(function(){var tagCorrel=JSON.parse($(this).attr('tagCorrel'));$(this).attr('stroke',colorFormat(tagCorrel.obs_end_val)).attr('stroke-width',(20*Math.pow(tagCorrel.obs_end_val,4)).toFixed(0)+'px').attr('zIndex',6).css('stroke-dasharray','0  1000').css('animation','dash 5s linear').css('animation-iteration-count','infinite').addClass('selectedLines')})}},},events:{mouseOut:function(){$('.selectedLines').removeClass('selectedLines').attr('stroke','black').attr('stroke-width',1).attr('zIndex',5)}}}},series:[{states:{hover:{color:'#BADA55'}},dataLabels:{enabled:!0,format:'{point.name}'},}]}
return o}
function colorFormat(v){var grMap=gradient.create([-1,-0.5,-0.15,0,0.3,0.5,0.8,1],['rgba(5,0,255,1)','rgba(0,132,255,.8)','rgba(0,212,255,.6)','rgba(0,255,204,.5)','rgba(253,255,53,.4)','rgba(255,160,0,.5)','rgba(255,50,0,.8)','rgba(255,0,122,1)'],'rgba');return gradient.valToColor(v,grMap,'rgba')};var gradient={create:function(arrayOfStops,arrayOfColors,inputColorType){var inputColorType=inputColorType||'rgba';var mapObj=[];if(arrayOfStops.length!==arrayOfColors.length)throw new Error('Error in gradientMap.create() - length of stops array !== length of colors array');if(arrayOfStops.length<2)throw new Error('Must contain at least two stops to create gradient!');if(['hex','rgb','rgba','htmlcolor'].indexOf(inputColorType)===-1)throw new Error('inputColorType must be "hex", "rgb", or "rgba"');for(var i=0;i<arrayOfStops.length-1;i++){var startColor=arrayOfColors[i];var endColor=arrayOfColors[i+1];if(inputColorType==='rgb'){startColor=this.rgbToRgba(startColor);endColor=this.rgbToRgba(endColor)}else if(inputColorType==='hex'){startColor=this.hexToRgba(startColor);endColor=this.hexToRgba(endColor)}else if(inputColorType==='htmlcolor'){startColor=this.htmlColorToRgba(startColor);endColor=this.htmlColorToRgba(endColor)}
mapObj.push({'start':arrayOfStops[i],'end':arrayOfStops[i+1],'startColor':startColor,'endColor':endColor})}
return mapObj},valToColor:function(val,mapObj,outputColorType){var outputColorType=outputColorType||'rgba';if(['hex','rgb','rgba'].indexOf(outputColorType)===-1)throw new Error('outputColorType must be "hex", "rgb", or "rgba"');for(var i=0;i<mapObj.length;i++){if(val<mapObj[i].start||val>mapObj[i].end)continue;var matchingRow=mapObj[i];break}
if(matchingRow==null)throw new Error('The value '+val+' does not lie within the min and max range of the gradient object (this happens if, e.g., your gradient has value stops of [1,50,100] and you tried to find the color for 101.');return this.twoPointGradient(val,matchingRow.start,matchingRow.end,matchingRow.startColor,matchingRow.endColor,outputColorType)},twoPointGradient:function(val,start,end,startColor,endColor,outputColorType){var rgbaArr=[];var rgba=null;var norm=(val-start)/(end-start);startColor=this.splitRgba(startColor);endColor=this.splitRgba(endColor);for(var i=0;i<=2;i++){rgbaArr[i]=parseInt(((endColor[i]-startColor[i])*norm).toFixed(0))+startColor[i]}
rgbaArr[3]=parseFloat(((endColor[3]-startColor[3])*norm+startColor[3]).toFixed(2));rgba=this.joinRgba(rgbaArr);if(outputColorType==='rgba')return rgba;if(outputColorType==='rgb')return this.rgbaToRgb(rgba);if(outputColorType==='hex')return this.rgbaToHex(rgba)},splitRgba:function(rgba){var fromArr=rgba.replace(/[^\d,.-]/g,'').split(',');var resArr=[];if(fromArr.length!==4)throw new Error('RGBA not 4 elements');for(i=0;i<=2;i++){if(fromArr[i]<0||fromArr[i]>255)throw new Error('RGB value not between 0 and 255: '+rgba);resArr[i]=parseInt(fromArr[i])}
if(fromArr[3]<0||fromArr[3]>1)throw new Error('Alpha value not between 0 and 1');resArr[3]=parseFloat(fromArr[3]);return resArr},joinRgba:function(rgbaArr){if(rgbaArr.length!==4)throw new Error('RGBA not 4 elements');return'rgba('+rgbaArr.join(',')+')'},rgbToRgba:function(rgb){var rgbArr=rgb.replace(/[^\d,.-]/g,'').split(',');if(rgbArr.length!==3)throw new Error('RGB not 3 elements');for(var i=0;i<=2;i++){if(rgbArr[i]<0||rgbArr[i]>255)throw new Error('RGB value not between 0 and 255: '+rgb);rgbArr[i]=parseInt(rgbArr[i])}
rgbArr.push(1.0);return this.joinRgba(rgbArr)},hexToRgba:function(hex){var c;if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){c=hex.substring(1).split('');if(c.length==3){c=[c[0],c[0],c[1],c[1],c[2],c[2]]}
c='0x'+c.join('');return'rgba('+[(c>>16)&255,(c>>8)&255,c&255].join(',')+',1)'}
throw new Error('Bad Hex')},htmlColorToRgba:function(htmlColor){var htmlMap=this.getHtmlNames();if(htmlMap.hasOwnProperty(htmlColor.toLowerCase())===!1)throw new Error('Invalid HTML Color Name: '+htmlColor);return this.hexToRgba(htmlMap[htmlColor.toLowerCase()])},rgbaToHex:function(rgba){var rgbaArr=this.splitRgba(rgba);function hex(x){return("0"+parseInt(x).toString(16)).slice(-2)}
return"#"+hex(rgbaArr[0])+hex(rgbaArr[1])+hex(rgbaArr[2])},rgbaToRgb:function(rgba){var rgbaArr=this.splitRgba(rgba);rgbaArr.pop();return'rgb('+rgbaArr.join(',')+')'},getHtmlNames:function(){return{"aliceblue":"#F0F8FF","antiquewhite":"#FAEBD7","aqua":"#00FFFF","aquamarine":"#7FFFD4","azure":"#F0FFFF","beige":"#F5F5DC","bisque":"#FFE4C4","black":"#000000","blanchedalmond":"#FFEBCD","blue":"#0000FF","blueviolet":"#8A2BE2","brown":"#A52A2A","burlywood":"#DEB887","cadetblue":"#5F9EA0","chartreuse":"#7FFF00","chocolate":"#D2691E","coral":"#FF7F50","cornflowerblue":"#6495ED","cornsilk":"#FFF8DC","crimson":"#DC143C","cyan":"#00FFFF","darkblue":"#00008B","darkcyan":"#008B8B","darkgoldenrod":"#B8860B","darkgray":"#A9A9A9","darkgrey":"#A9A9A9","darkgreen":"#006400","darkkhaki":"#BDB76B","darkmagenta":"#8B008B","darkolivegreen":"#556B2F","darkorange":"#FF8C00","darkorchid":"#9932CC","darkred":"#8B0000","darksalmon":"#E9967A","darkseagreen":"#8FBC8F","darkslateblue":"#483D8B","darkslategray":"#2F4F4F","darkslategrey":"#2F4F4F","darkturquoise":"#00CED1","darkviolet":"#9400D3","deeppink":"#FF1493","deepskyblue":"#00BFFF","dimgray":"#696969","dimgrey":"#696969","dodgerblue":"#1E90FF","firebrick":"#B22222","floralwhite":"#FFFAF0","forestgreen":"#228B22","fuchsia":"#FF00FF","gainsboro":"#DCDCDC","ghostwhite":"#F8F8FF","gold":"#FFD700","goldenrod":"#DAA520","gray":"#808080","grey":"#808080","green":"#008000","greenyellow":"#ADFF2F","honeydew":"#F0FFF0","hotpink":"#FF69B4","indianred":"#CD5C5C","indigo":"#4B0082","ivory":"#FFFFF0","khaki":"#F0E68C","lavender":"#E6E6FA","lavenderblush":"#FFF0F5","lawngreen":"#7CFC00","lemonchiffon":"#FFFACD","lightblue":"#ADD8E6","lightcoral":"#F08080","lightcyan":"#E0FFFF","lightgoldenrodyellow":"#FAFAD2","lightgray":"#D3D3D3","lightgrey":"#D3D3D3","lightgreen":"#90EE90","lightpink":"#FFB6C1","lightsalmon":"#FFA07A","lightseagreen":"#20B2AA","lightskyblue":"#87CEFA","lightslategray":"#778899","lightslategrey":"#778899","lightsteelblue":"#B0C4DE","lightyellow":"#FFFFE0","lime":"#00FF00","limegreen":"#32CD32","linen":"#FAF0E6","magenta":"#FF00FF","maroon":"#800000","mediumaquamarine":"#66CDAA","mediumblue":"#0000CD","mediumorchid":"#BA55D3","mediumpurple":"#9370DB","mediumseagreen":"#3CB371","mediumslateblue":"#7B68EE","mediumspringgreen":"#00FA9A","mediumturquoise":"#48D1CC","mediumvioletred":"#C71585","midnightblue":"#191970","mintcream":"#F5FFFA","mistyrose":"#FFE4E1","moccasin":"#FFE4B5","navajowhite":"#FFDEAD","navy":"#000080","oldlace":"#FDF5E6","olive":"#808000","olivedrab":"#6B8E23","orange":"#FFA500","orangered":"#FF4500","orchid":"#DA70D6","palegoldenrod":"#EEE8AA","palegreen":"#98FB98","paleturquoise":"#AFEEEE","palevioletred":"#DB7093","papayawhip":"#FFEFD5","peachpuff":"#FFDAB9","peru":"#CD853F","pink":"#FFC0CB","plum":"#DDA0DD","powderblue":"#B0E0E6","purple":"#800080","rebeccapurple":"#663399","red":"#FF0000","rosybrown":"#BC8F8F","royalblue":"#4169E1","saddlebrown":"#8B4513","salmon":"#FA8072","sandybrown":"#F4A460","seagreen":"#2E8B57","seashell":"#FFF5EE","sienna":"#A0522D","silver":"#C0C0C0","skyblue":"#87CEEB","slateblue":"#6A5ACD","slategray":"#708090","slategrey":"#708090","snow":"#FFFAFA","springgreen":"#00FF7F","steelblue":"#4682B4","tan":"#D2B48C","teal":"#008080","thistle":"#D8BFD8","tomato":"#FF6347","turquoise":"#40E0D0","violet":"#EE82EE","wheat":"#F5DEB3","white":"#FFFFFF","whitesmoke":"#F5F5F5","yellow":"#FFFF00","yellowgreen":"#9ACD32"}}}