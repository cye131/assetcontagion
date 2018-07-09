$(document).ready(function(){$(".nav-item").each(function(){var a=$(this).find('a:first');var link=a.attr("href");var pathname=window.location.pathname;if(link===pathname){$(this).addClass('active');return}})});function getData(){return $('#data').data()}
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
function getCorrMath(corr_type){if(corr_type==='rho')return"Pearson's Correlation Coefficient";else if(corr_type==='ktau')return" \\tau = \\frac{2}{n(n-1)} \\sum_{i=1}^{N} \\sum_{j=i}^{N} \\begin{cases} 1, & \\text{if } (x_i > x_j \\text{ and } y_j > y_j)  \\text{ or } (x_i < x_j \\text{ and } y_j < y_j)  \\\\0, &\\text{if } x_i = x_j \\text{ or } y_i = y_j  \\\\-1, &\\text{otherwise} \\end{cases}";else if(corr_type==='mic')return"Maximal Information Coefficient";else if(corr_type==='srho')return"Spearman's Rho"};$(document).ready(function(){(function(){var category='reg';var corr_type=sessionStorage.getItem('corr_type')||'rho';var freq=sessionStorage.getItem('freq')||'d';var trail=sessionStorage.getItem('trail')||30;var data={};$('#overlay').show();var d1=$.Deferred(function(dfd){var ajaxGetSpecsCategories=getAJAX(['get_specs_categories'],[],['specsCategories'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);var ajaxGetTagsSeries=getAJAX(['get_tags_series'],[],['tagsSeries'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);var ajaxGetTagsCorrel=getAJAX(['get_tags_correl'],[],['tagsCorrel'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);$.when(ajaxGetSpecsCategories,ajaxGetTagsSeries,ajaxGetTagsCorrel).done(function(r1,r2,r3){$.extend(!0,data,{dataInfo:{"category":category,"corr_type":corr_type,"freq":freq,"trail":trail},specsCategories:JSON.parse(r1[0]).specsCategories,tagsSeries:JSON.parse(r2[0]).tagsSeries,tagsCorrel:JSON.parse(r3[0]).tagsCorrel,tagsGFI:[]});$.each(data.tagsCorrel,function(i,row){if(row.grouping_1==='World.'||row.grouping_2==='World.')data.tagsGFI.push(row)});setCategoryOptions(data);drawHeatMap(data.tagsSeries,data.tagsCorrel);drawMaps(data.tagsGFI);drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,0.75);drawStrongCorrelationsEurope(data.tagsSeries,data.tagsCorrel,0.75);$("#highMapEurope > div").addClass('float-right').css('margin-top','-60px');dfd.resolve(data);return dfd.promise()})});var d2=$.Deferred(function(dfd){var ajaxGetHistCorrIndex=getAJAX(['get_hist_corr_index'],[],['histCorrIndex'],{'category':category,'corr_type':corr_type,'freq':freq,'trail':trail},10000,1);ajaxGetHistCorrIndex.done(function(res){var histCorrIndex=JSON.parse(res).histCorrIndex;drawHeatMapDates(histCorrIndex);var dates=[];$.each(histCorrIndex,function(i,row){dates.push(row.pretty_date)});$.extend(!0,data,{'hmDates':dates,"playState":'pause',"playIndex":dates.length-1,})});dfd.resolve(data);return dfd.promise()});$.when(d1,d2).done(function(data1,data2){$.extend(!0,data,data1,data2);setData(data);$('#overlay').hide()})})();$('#heatmap').on('click','#heatmap-subtitle-group > button.heatmap-subtitle',function(){var data=getData();if(data.playState==null)return;if($(this).data('dir')==='pause'){data.playState='pause'}
if($(this).data('dir')==='start'||$(this).data('dir')==='end'){if($(this).data('dir')==='start')data.playIndex=0;else data.playIndex=data.hmDates.length-1;data.playState='pause'}else if($(this).data('dir')==='back'||$(this).data('dir')==='forward'){if($(this).data('dir')==='back')data.playIndex=(data.playIndex>=5?data.playIndex-5:0);else data.playIndex=(data.playIndex+5<=data.hmDates.length-1?data.playIndex+5:data.hmDates.length);data.playState=$(this).data('dir')}
setData(data);if($(this).data('dir')!=='pause')updateCharts($('#heatmap').highcharts(),$('#heatmap-dates').highcharts());return});$('a[data-toggle="tab"]').on('shown.bs.tab',function(){if($('.successmsg').text().length>0){$('.chart').each(function(){$(this).highcharts().reflow()})}});$("#corrselector").submit(function(e){e.preventDefault();var freqtrail=$('#freqtrail').val();freqtrail=freqtrail.split('.');$('#freq').val(freqtrail[0]);$('#trail').val(freqtrail[1]);window.sessionStorage.setItem("freq",freqtrail[0]);window.sessionStorage.setItem("trail",freqtrail[1]);window.sessionStorage.setItem("corr_type",$('#corr_type').val());$("#corrselector")[0].submit()});$('#corr-type-dropdown').on('click','a.corr-type',function(){$('#corr-type-dropdown-text').html($(this).html()).find('span').tooltip();$(this).hide().siblings().show()});$('#corrselector').on('shown.bs.tooltip','[data-toggle="tooltip"]',function(){var tooltipID=$(this).attr('aria-describedby');var tooltip=$('#'+tooltipID);MathJax.Hub.Queue(["Typeset",MathJax.Hub,tooltip[0]])})});function setCategoryOptions(data){if(data.specsCategories.length!==1)console.log('ERR: No category selected');else{var freqtrails=data.specsCategories[0].cat_freqtrails.split(',');var selectedfreqtrail=(typeof sessionStorage.getItem('freq')!==undefined&&typeof sessionStorage.getItem('trail')!==undefined)?sessionStorage.getItem('freq')+'.'+sessionStorage.getItem('trail'):null;for(i=0;i<freqtrails.length;i++){var freqtrails_split=freqtrails[i].split('.');var freqtrails_str=freqtrails_split[1]+(freqtrails_split[0]==='d'?'-day':freqtrails_split[0])+' '+(freqtrails_split[0]==='d'?'Rolling Correlation':'Correlation');$('#freqtrail').append('<option value="'+freqtrails[i]+'" '+(freqtrails[i]===selectedfreqtrail?'selected':'')+' >'+freqtrails_str+'</option>')}
var corr_types=data.specsCategories[0].cat_corrtypes.split(',');var selectedcorr_type=(typeof sessionStorage.getItem('corr_type')!==undefined)?sessionStorage.getItem('corr_type'):null;for(i=0;i<corr_types.length;i++){var corr_type_str=(corr_types[i]==='rho'?'Pearson Correlation':(corr_types[i]==='ktau'?"Kendall's &#120533; Coefficient":(corr_types[i]==='mic'?'Maximal Information Coefficient (MIC)':'')));$('#corr_type').append('<option value="'+corr_types[i]+'" '+(corr_types[i]===selectedcorr_type?'selected':'')+' >'+corr_type_str+'</option>')}
for(i=0;i<corr_types.length;i++){var corr_type_str=getCorrName(corr_types[i]);if(corr_types[i]===selectedcorr_type)$('#corr-type-dropdown-text').html(corr_type_str+'&nbsp;<span href="#" data-toggle="tooltip" title="$$\tau = \sqrt{b^2} \\sum_{i} \sum_{j=1}$$" class="badge badge-primary">'+'test'+'</span>');$('#corr-type-dropdown').append('<a class="dropdown-item corr-type" href="#" data-corr-type="'+corr_types[i]+'"  '+(corr_types[i]===selectedcorr_type?'style="display:none"':'')+' >'+corr_type_str+'&nbsp;<span href="#" data-toggle="tooltip" title="$$'+getCorrMath(corr_types[i])+'$$" class="badge badge-primary">'+'[info]'+'</span>'+'</a>')}
$('#corr-type-dropdown-text span[data-toggle="tooltip"], #corr-type-dropdown span[data-toggle="tooltip"]').tooltip()}}
function updateCharts(chartHM,chartHMDates){var timeStart=new Date().getTime();var data=getData();if(data.hmDates===undefined||data.playIndex===undefined)return;var date=data.hmDates[data.playIndex];updatePlotLine(chartHMDates,new Date(date).getTime());var ajaxGetHistCorrel=getAJAX(['get_hist_correl_by_date'],[],['tagsCorrel',],{"date":date,"category":data.dataInfo.category,"corr_type":data.dataInfo.corr_type,"freq":data.dataInfo.freq,"trail":data.dataInfo.trail},20000,'disabled');ajaxGetHistCorrel.done(function(res){if(JSON.parse(res).tagsCorrel.length==null)return;var tagsCorrelDate=JSON.parse(res).tagsCorrel;var hm=drawHeatMap(data.tagsSeries,tagsCorrelDate,1);for(j=0;j<hm.data.length;j++){chartHM.series[0].data[j].update(hm.data[j],!1)}
chartHM.redraw();$('#heatmap-subtitle-date').text(Highcharts.dateFormat('%m/%d/%Y',new Date(date).getTime()));if(data.playIndex<=0){data.playState='pause';data.playIndex=0}else if(data.playIndex>=data.hmDates.length-1){data.playState='pause';data.playIndex=data.hmDates.length-1}else{if(data.playState==='forward')data.playIndex=data.playIndex+5;else data.playIndex=data.playIndex-5}
setData(data);updateHMButtons();var timeEnd=new Date().getTime();var timeWait=timeEnd-timeStart<500?500-(timeEnd-timeStart):500;if(data.playState!=='pause')setTimeout(function(){updateCharts(chartHM,chartHMDates)},timeWait)})}
function updateHMButtons(){var data=getData();var buttons=$('#heatmap-subtitle-group').find('button.heatmap-subtitle').removeClass('active').prop('disabled',!1).end();if(data.playIndex===0){buttons.find('[data-dir="start"],[data-dir="back"]').prop('disabled',!0);return}
if(data.playIndex===data.hmDates.length-1){buttons.find('[data-dir="end"],[data-dir="forward"]').prop('disabled',!0);return}
if(data.playState==='pause'){buttons.find('[data-dir="pause"]').addClass('active',!0);return}
if(data.playState==='back'){buttons.find('[data-dir="back"]').addClass('active',!0);return}
if(data.playState==='forward'){buttons.find('[data-dir="forward"]').addClass('active',!0);return}};function drawHeatMap(tagsSeries,tagsCorrel,noChart){var hm={"data":[],"info":{}};var vals=[];var val;var lastDate=new Date('1970-01-01');if(tagsSeries.length===0||tagsCorrel.length===0)return;var i=0;for(x=0;x<tagsSeries.length;x++){for(y=0;y<tagsSeries.length;y++){hm.data[i]={"s_id_x":parseInt(tagsSeries[x].s_id),"s_id_y":parseInt(tagsSeries[y].s_id),"x":x,"y":y,"color":(x===y)?'rgba(20,20,20,.1)':null,"tooltip":!1};$.each(tagsCorrel,function(index,tagRow){if((tagRow.b_id_1!==tagsSeries[x].b_id||tagRow.b_id_2!==tagsSeries[y].b_id)&&(tagRow.b_id_1!==tagsSeries[y].b_id||tagRow.b_id_2!==tagsSeries[x].b_id))return!0;val=tagRow.h_value||tagRow.obs_end_val||null;val=parseFloat(val).toFixed(4);vals.push(val);$.extend(!0,hm.data[i],{"pretty_date":tagRow.h_pretty_date||tagRow.obs_end,"value":val,"obs_start":tagRow.obs_start,"obs_end":tagRow.obs_end,"freq":tagRow.freq,"trail":tagRow.trail,"last_updated":tagRow.last_updated,"tooltip":(val==null)?!1:!0});if(new Date(hm.data[i].pretty_date)>lastDate)lastDate=new Date(hm.data[i].pretty_date)});i++}}
var titles=[];$.each(tagsSeries,function(i,row){titles.push(row.name)});$.extend(hm.info,{'min':Math.min(...vals),'max':Math.max(...vals),'titles':titles,'colorarray':{}});j=0;var group='';var usedGroups=[];$.each(tagsSeries,function(i,tag){if(tag.grouping==null)return!0;group=tag.grouping.split('.')[0];if(usedGroups.indexOf(group)!==-1)return!0;usedGroups.push(group);hm.info.colorarray[group]=getColorArray()[j];j++});if(noChart)return hm;var o=getHighChartsOptions();$.extend(!0,o,{chart:{height:1060,marginTop:100,marginRight:130,marginBottom:200,marginLeft:130,plotBorderWidth:1,backgroundColor:null},title:{text:'Correlation Matrix Between Stock Markets of Major Economies'},subtitle:{enabled:!0,useHTML:!0,style:{width:'100%',"z-index":1},text:'<div class="row text-center"><div class="col-12 d-inline-block">'+'<h4 class="text-secondary"><span class="badge badge-secondary">*Data for&nbsp;<span id="heatmap-subtitle-date">'+Highcharts.dateFormat('%m/%d/%Y',lastDate)+'</span></span></h4>'+'</div></div>'+'<div class="row text-center"><div class="col-12 btn-group d-inline-block" role="group" id="heatmap-subtitle-group">'+'<button class="btn btn-secondary btn-sm" type="button" disabled>Click to show changes over time&nbsp;</button>'+'<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="start" style="letter-spacing:-2px">&#10074;&#9664;&#9664;</button>'+'<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="back">&#9664;</button>'+'<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="pause" disabled>&#10074;&#10074;</button>'+'<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="forward" disabled>&#9654;</button>'+'<button class="btn btn-primary btn-sm heatmap-subtitle" type="button" data-dir="end" style="letter-spacing:-2px" disabled>&#9654;&#9654;&#10074;</button>'+'</div></div>'},credits:{enabled:!1},xAxis:{categories:hm.info.titles,labels:{formatter:function(){var thiscat;for(i=0;i<tagsSeries.length;i++)if(tagsSeries[i].name===this.value){thiscat=tagsSeries[i].grouping.split('.')[0];break}
return'<span style="font-weight:bold;color:'+hm.info.colorarray[thiscat]+'">'+this.value+'</span>'},rotation:-90,y:15}},yAxis:{categories:hm.info.titles,title:null,labels:{formatter:function(){var thiscat;for(i=0;i<tagsSeries.length;i++)if(tagsSeries[i].name===this.value){thiscat=tagsSeries[i].grouping.split('.')[0];break}
return'<span style="font-weight:bold;color:'+hm.info.colorarray[thiscat]+'">'+this.value+'</span>'},rotation:-45}},colorAxis:{min:-1,max:1,reversed:!1,stops:[[0,'rgba(5,0,255,1)'],[0.2,'rgba(0,132,255,.8)'],[0.3,'rgba(0,212,255,.6)'],[0.5,'rgba(179,255,179,.3)'],[0.7,'rgba(253,255,53,.4)'],[0.8,'rgba(255,160,0,.5)'],[0.9,'rgba(255,50,0,.8)'],[1,'rgba(255,0,112,1)']]},legend:{enabled:!0,layout:'vertical',align:'right',verticalAlign:'top',reversed:!0,symbolHeight:800,y:40},tooltip:{useHTML:!0,style:{"z-index":10},formatter:function(){if(this.point.tooltip===!0){var cat_x='';var cat_y='';var proxy_x='';var proxy_y='';for(i=0;i<tagsSeries.length;i++){if(tagsSeries[i].s_id===this.point.s_id_x){cat_x=tagsSeries[i].grouping.split('.')[0];proxy_x=tagsSeries[i].proxy}
if(tagsSeries[i].s_id===this.point.s_id_y){cat_y=tagsSeries[i].grouping.split('.')[0];proxy_y=tagsSeries[i].proxy}}
var text='<table>'+'<tr><td style="text-align:center;font-weight:600">'+'Trailing '+this.point.trail+this.point.freq+' correlation between '+'<span style="font-weight:700;color:'+hm.info.colorarray[cat_x]+'">'+this.series.xAxis.categories[this.point.x]+'\xB9</span>'+' and '+'<span style="font-weight:700;color:'+hm.info.colorarray[cat_y]+'">'+this.series.yAxis.categories[this.point.y]+'\xB2</span>'+' on <span style="font-weight:bold">'+this.point.pretty_date+'</span>'+' : '+'<span style="font-weight:700;color:'+(this.point.value>0?'rgba(255,0,0,1)':'rgba(0,0,255,1)')+'">'+(this.point.value>0?'+':'')+this.point.value+'</span>'+'</td></tr>'+'<tr style="height:1.0em"><td></td></tr>'+'<tr><td style="text-align:right;font-size:0.7em;">'+'Series updated <span>'+this.point.last_updated+' ET</span>'+'<br><span style="font-weight:700;color:'+hm.info.colorarray[cat_x]+'">\xB9</span> <span style="text-align:right">Calculated using market prices of '+proxy_x+' as a proxy</span>'+'<br><span style="font-weight:700;color:'+hm.info.colorarray[cat_y]+'">\xB2</span> <span style="text-align:right">Calculated using market prices of '+proxy_y+' as a proxy</span>'+'</td></tr>'+'</table>';return text}else{return!1}}},series:[{name:'Correlation',borderColor:'rgba(255,255,255,0)',borderWidth:1,type:'heatmap',animation:{duration:500},data:hm.data,dataLabels:{enabled:!0,formatter:function(){if(hm.info.titles.length>10){return''}else if(this.point.tooltip===!0){return(this.point.value*100).toFixed(2)+'%'}else{return!1}}},turboThreshold:0}]});var chart=new Highcharts.chart('heatmap',o);var boxW=chart.xAxis[0].width/chart.xAxis[0].categories.length;var boxH=chart.yAxis[0].height/chart.yAxis[0].categories.length;var offsetH=chart.plotSizeY+chart.plotTop;var offsetW=chart.plotLeft;j=0;group='';usedGroups=[];var groups=[];var groupsIndex=0;$.each(tagsSeries,function(i,tag){if(tag.grouping==null)return!0;group=tag.grouping.split('.')[0];if(usedGroups.indexOf(group)===-1){usedGroups[j]=group;groups[j]={"start":i,"end":i,"grouping":group};j++}else{groupsIndex=usedGroups.indexOf(group);groups[groupsIndex].end=i}});var xticks=chart.xAxis[0].ticks;var yticks=chart.yAxis[0].ticks;for(i=0;i<Object.keys(xticks).length-1;i++){if(typeof groups[i]!=='undefined'){groups[i].XAXISy=xticks[groups[i].start].label.attr('y');groups[i].YAXISx=yticks[groups[i].start].label.attr('x');groups[i].yTop=offsetH-(groups[i].start)*boxH;groups[i].yBottom=offsetH-(groups[i].end+1)*boxH;groups[i].xLeft=offsetW+groups[i].start*boxW;groups[i].xRight=offsetW+(groups[i].end+1)*boxW;groups[i].color=hm.info.colorarray[groups[i].grouping]}}
groups.forEach(function(group,index){chart.renderer.path(['M',group.start*boxW+offsetW+2,group.XAXISy-5,'L',(group.end+1)*boxW+offsetW-2,group.XAXISy-5]).attr({'stroke-width':2,stroke:group.color}).add();chart.renderer.path(['M',group.YAXISx+5,offsetH-group.start*boxH-5,'L',group.YAXISx+5,offsetH-(group.end+1)*boxH+5]).attr({'stroke-width':2,stroke:group.color}).add();(function(){if(group.grouping==='World')return;var text=chart.renderer.text(group.grouping,offsetW+group.start*boxW+(group.end+1-group.start)*boxW/2,group.XAXISy+100).attr({zIndex:5,'text-anchor':'middle'}).css({textAlign:'center',color:'white'}).add();box=text.getBBox();chart.renderer.rect(box.x-5,box.y-5,box.width+10,box.height+10,5).attr({fill:group.color,stroke:'gray','stroke-width':1,zIndex:4}).add()})();var paths=[];paths[0]=['M',group.xLeft,group.yTop,'H',group.xRight];paths[1]=['M',group.xLeft,group.yBottom,'H',group.xRight];paths[2]=['M',group.xLeft,group.yBottom,'V',group.yTop];paths[3]=['M',group.xRight,group.yBottom,'V',group.yTop];for(i=0;i<paths.length;i++){chart.renderer.path(paths[i]).attr({'stroke-width':2,"zIndex":5,"stroke":group.color}).add()}
fontSize=Math.round((group.xRight-group.xLeft)/5,0);if(fontSize<6){}else{if(fontSize<8)fontSize=8;if(fontSize>20)fontSize=20;chart.renderer.text(group.grouping.replace(' ','<br>'),(group.xLeft+group.xRight)/2,(group.yTop+group.yBottom)/2).attr({zIndex:6,'text-anchor':'middle'}).css({"textAlign":'center',"color":group.color,"opacity":0.8,'font-size':fontSize+'px'}).add()}})}
function drawHeatMapDates(histCorrIndex){var hmd=[];var vals=[];for(i=0;i<histCorrIndex.length;i++){hmd.push({"x":new Date(histCorrIndex[i].pretty_date).getTime(),"y":parseFloat(histCorrIndex[i].value)});vals.push(parseFloat(histCorrIndex[i].value))}
var hmcol=[];for(i=0;i<histCorrIndex.length;i++){hmcol.push({"x":new Date(histCorrIndex[i].pretty_date).getTime(),"low":-1,"high":1})}
hmcol.push({"x":hmcol[hmcol.length-1].x+1000*60*60*24,"low":null,"high":null});var o=getHighChartsOptions();$.extend(!0,o,{chart:{backgroundColor:'rgba(255,255,255,0)',marginRight:50,marginLeft:50},credits:{enabled:!1},title:{text:'Average '+(sessionStorage.getItem('trail')||'30')+(sessionStorage.getItem('freq')||'d')+' cross-regional equity correlation ('+(getCorrName(sessionStorage.getItem('corr_type'))||'Pearson Correlation')+')',useHTML:!0},subtitle:{text:null},plotOptions:{series:{dataGrouping:{enabled:!0,units:[['day',[1]]]},cursor:'pointer',point:{events:{click:function(){var data=getData();var d=new Date(this.x).toISOString().split('T')[0];if(data.hmDates.indexOf(d)===-1)return;$.extend(!0,data,{"playIndex":data.hmDates.indexOf(d),"playState":"pause"});setData(data);updateCharts($('#heatmap').highcharts(),this.series.chart)}}}}},tooltip:{useHTML:!0,formatter:function(){return'<h6 class="text-center;" style="font-weight:bold">'+Highcharts.dateFormat('%b \ %e \ %Y',this.points[0].x)+'</h6></br>'+'Average Correlation: '+Highcharts.numberFormat(this.points[0].y,4)},shared:!0},yAxis:{max:1,min:-1,startOnTick:!1,endOnTick:!1,opposite:!1,showLastLabel:!0,labels:{formatter:function(){return this.value.toFixed(1)}},plotLines:[{value:0,width:1,color:'black',dashStyle:'Dash',zIndex:2},{value:1,width:1,color:'rgba(10, 24, 66,1)',zIndex:5},{value:-1,width:1,color:'rgba(10, 24, 66,1)',zIndex:5}]},xAxis:{dateTimeLabelFormats:{day:"%m-%d-%Y",week:"%m-%d-%Y"},labels:{}},navigator:{enabled:!1},series:[{data:hmd,turboThreshold:0,id:'s01',type:'line',color:'#333',marker:{enabled:!1},zIndex:1,},{type:'arearange',data:hmcol,turboThreshold:0,linkedTo:'s01',zIndex:0,fillColor:'rgba(255,255,255,0)',lineColor:'rgba(255,255,255,0)'},{type:'sma',linkedTo:'s01',params:{period:90},zIndex:1,marker:{enabled:!1},visible:!1}]});var c=new Highcharts.stockChart('heatmap-dates',o);updatePlotLine(c,hmd[hmd.length-1].x)}
function updatePlotLine(chart,date){chart.xAxis[0].removePlotLine('plot-line');chart.xAxis[0].addPlotLine({value:date,color:'rgba(255,0,0,.5)',width:5,id:'plot-line',zIndex:3,label:{text:'<h6 class="text-danger">Currently displayed date</h6>',align:'left',verticalAlign:'top',rotation:90,useHTML:!0}})};$(document).ready(function(){(function(){})();$('#showLines').change(function(){if($(this).is(":checked")){$("#minLines").removeAttr("disabled")}else{$("#minLines").attr("disabled","disabled");$(".connectLines").remove()}});$("#submitLines").click(function(){$('#minLines').removeClass('is-invalid');var x=parseFloat($("#minLines").val());if(x<0||x>1||$.isNumeric(x)===!1){$('#minLines').addClass('is-invalid');$(".invalid-feedback").show();$(".invalid-feedback").text('Input must be between 0 and 1')}else{$(".connectLines").remove();var data=$('#data').data();drawStrongCorrelations(data.tagsSeries,data.tagsCorrel,x);drawStrongCorrelationsEurope(data.tagsSeries,data.tagsCorrel,x)}})});function drawMaps(tagsGFI){var mapData=[];var gfiIndex=0;var j=0;for(i=0;i<tagsGFI.length;i++){gfiIndex=(tagsGFI[i].code_2==="WORLD")?1:2;if(tagsGFI[i]['code_'+gfiIndex].toLowerCase()==='hk')continue;mapData[j]={};mapData[j]['hc-key']=tagsGFI[i]['code_'+gfiIndex].toLowerCase();mapData[j].value=tagsGFI[i].obs_end_val;mapData[j].name=tagsGFI[i]['name_'+gfiIndex];mapData[j].id=mapData[j]['hc-key'];mapData[j].grouping=tagsGFI[i]['grouping_'+gfiIndex];j++}
var mapDataEurope=[];var k=0;for(i=0;i<mapData.length;i++){if(mapData[i].grouping.indexOf('Europe')!==-1){mapDataEurope[k]=mapData[i];k++}}
drawMap(mapData);drawMapEurope(mapDataEurope)}
function drawMap(mapData){Highcharts.mapChart('highMap',{chart:{map:'custom/world-robinson',height:640,marginTop:0,marginRight:0,marginBottom:0,marginLeft:0,backgroundColor:'rgba(255,255,255,0)',},title:{text:'Correlation to Global Stock Markets By Country'},subtitle:{},credits:{enabled:!1},mapNavigation:{enabled:!1},colorAxis:{min:-1,max:1,reversed:!1,stops:[[0,'rgba(0,0,255,1)'],[0.5,'rgba(251,250,182,1)'],[1,'rgba(255,0,0,1)']]},legend:{enabled:!1,layout:'horizontal',align:'center',verticalAlign:'top',reversed:!0},plotOptions:{series:{point:{events:{mouseOver:function(){code=this["hc-key"];$('.'+code).each(function(){$(this).addClass('selectedLines')})}},},events:{mouseOut:function(){$('.selectedLines').removeClass('selectedLines')}}}},series:[{data:mapData,name:'Correlation to Global Stock Market Index',states:{hover:{color:'#BADA55'}},dataLabels:{enabled:!0,format:'{point.name}'},}]})}
function drawStrongCorrelations(tagsSeries,tagsCorrel,minLines){var chart=$('#highMap').highcharts();var points=chart.series[0].points;tagsSeriesXY={};for(i=0;i<points.length;i++){for(k=0;k<tagsSeries.length;k++){if(tagsSeries[k].code.toLowerCase()===points[i]["hc-key"]){tagsSeriesXY[points[i]["hc-key"]]={};tagsSeriesXY[points[i]["hc-key"]].x=points[i].plotX;tagsSeriesXY[points[i]["hc-key"]].y=points[i].plotY}}}
for(i=0;i<tagsCorrel.length;i++){if(tagsCorrel[i].obs_end_val<minLines)continue;code1=tagsCorrel[i].code_1.toLowerCase();code2=tagsCorrel[i].code_2.toLowerCase();if(code1==='hk'||code2==='hk'||code1==='world'||code2==='world')continue;chart.renderer.path(['M'+tagsSeriesXY[code1].x,tagsSeriesXY[code1].y,'Q',(tagsSeriesXY[code1].x+tagsSeriesXY[code2].x)/2,(tagsSeriesXY[code1].y+tagsSeriesXY[code2].y)/2-20,tagsSeriesXY[code2].x,tagsSeriesXY[code2].y,]).attr({'stroke-width':1,zIndex:5,stroke:'#696969',class:'connectLines '+code1+'  '+code2}).add()}}
function drawMapEurope(mapDataEurope){Highcharts.mapChart('highMapEurope',{chart:{map:'custom/europe',height:360,width:460,marginTop:10,marginRight:120,marginBottom:10,marginLeft:10,backgroundColor:'rgba(255,255,255,0)',plotBorderWidth:1,plotBorderColor:'black'},title:{text:''},credits:{enabled:!1},mapNavigation:{enabled:!1},colorAxis:{min:-1,max:1,reversed:!1,stops:[[0,'rgba(0,0,255,1)'],[0.5,'rgba(251,250,182,0)'],[1,'rgba(255,0,0,1)']]},legend:{enabled:!1,layout:'horizontal',align:'center',verticalAlign:'top',reversed:!0},plotOptions:{series:{point:{events:{mouseOver:function(){code=this["hc-key"];$('.'+code).each(function(){$(this).addClass('selectedLines')})}},},events:{mouseOut:function(){$('.selectedLines').removeClass('selectedLines')}}}},series:[{data:mapDataEurope,name:'Correlation to Global Stock Market Index',states:{hover:{color:'#BADA55'}},dataLabels:{enabled:!0,format:'{point.name}'}}]})}
function drawStrongCorrelationsEurope(tagsSeries,tagsCorrel,minLines){var chart=$('#highMapEurope').highcharts();var points=chart.series[0].points;tagsSeriesXY={};for(i=0;i<points.length;i++){for(k=0;k<tagsSeries.length;k++){if(tagsSeries[k].code.toLowerCase()===points[i]["hc-key"]){tagsSeriesXY[points[i]["hc-key"]]={};tagsSeriesXY[points[i]["hc-key"]].x=parseFloat(points[i].plotX)+chart.plotLeft;tagsSeriesXY[points[i]["hc-key"]].y=parseFloat(points[i].plotY)+chart.plotTop}}}
for(i=0;i<tagsCorrel.length;i++){if(tagsCorrel[i].obs_end_val<minLines)continue;if(tagsCorrel[i].grouping_1==undefined||tagsCorrel[i].grouping_2==undefined||tagsCorrel[i].grouping_1.indexOf('Europe')==-1||tagsCorrel[i].grouping_2.indexOf('Europe')==-1)continue;code1=tagsCorrel[i].code_1.toLowerCase();code2=tagsCorrel[i].code_2.toLowerCase();chart.renderer.path(['M'+tagsSeriesXY[code1].x,tagsSeriesXY[code1].y,'Q',(tagsSeriesXY[code1].x+tagsSeriesXY[code2].x)/2,(tagsSeriesXY[code1].y+tagsSeriesXY[code2].y)/2-20,tagsSeriesXY[code2].x,tagsSeriesXY[code2].y,]).attr({'stroke-width':1,zIndex:5,stroke:'#696969',class:'connectLines '+code1+'  '+code2}).add()}}