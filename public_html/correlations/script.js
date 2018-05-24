/*jshint sub:true*/

function ucFirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
}
/*
function prepCSV () {
    var csvObj = [];
    var keys = [], i = 0; for (keys[i++] in results[0].csv_data) {}
    console.log(keys);
    
    for (i = 0; i < keys.length; i++) {
        console.log(keys[i]);
        var datekey = keys[i];
        csvObj[i] = [];
        csvObj[i][0] = datekey;
        
        if (typeof results[0].csv_data[datekey]['data1_roi'] != 'undefined') {
            csvObj[i][1] = results[0].csv_data[datekey]['data1_roi'];
        } else {
            csvObj[i][1] = 'N/A';
        }

        if (typeof results[0].csv_data[datekey]['data2_roi'] != 'undefined') {
            csvObj[i][2] = results[0].csv_data[datekey]['data2_roi'];
        } else {
            csvObj[i][2] = 'N/A';
        }

        if (typeof results[0].csv_data[datekey]['correlation'] != 'undefined') {
            csvObj[i][3] = results[0].csv_data[datekey]['correlation'];
        } else {
            csvObj[i][3] = 'N/A';
        }
    }
    console.log(csvObj);
    exportToCsv('export.csv', csvObj);
}

function exportToCsv(filename, rows) {
        var processRow = function (row) {
            var finalVal = '';
            for (var j = 0; j < row.length; j++) {
                var innerValue = row[j] === null ? '' : row[j].toString();
                if (row[j] instanceof Date) {
                    innerValue = row[j].toLocaleString();
                }
                var result = innerValue.replace(/"/g, '""');
                if (result.search(/("|,|\n)/g) >= 0)
                    result = '"' + result + '"';
                if (j > 0)
                    finalVal += ',';
                finalVal += result;
            }
            return finalVal + '\n';
        };

        var csvFile = '';
        for (var i = 0; i < rows.length; i++) {
            csvFile += processRow(rows[i]);
        }

    var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}
*/

$(document).ready(function() {
    /* UI Elements */
    
    /* Highlights navigation bar menu item if it's active */
    $(".nav-item").each(function(){
        var a = $(this).find('a:first');
        var link = a.attr("href");
        var pathname = window.location.pathname;
        //$(this).addClass(link); testing
        //$(this).addClass(pathname);
        if (link == pathname) $(this).addClass('active');
    });

    function startSpinner() {
        $('#spinnercontainer').show();
        $('.sk-circle').show();
        $('#resultscontainer').hide();
    }
    
    function endSpinner() {
        $('#spinnercontainer').hide();
        $('.sk-circle').hide();
        $('#resultscontainer').show();
    }

    function editSpinner(str) {
        $('#loadmessage').text(str);
    }

    
    //Resizes width after clicking each tab, otherwise highcharts is too skinny!
    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        if ($('.successmsg').text().length > 0) {
        $( '.chart' ).each(function() { 
            $(this).highcharts().reflow();
        });
        }
    });
    
    
    /** Calculations **/

    $("#submit").click(function(){
        emptyCheck();
    });
    
    function validateFail(str){
        $('#stock').addClass('is-invalid');
        $( ".invalid-feedback" ).show();
        $( ".invalid-feedback" ).text(str);
    }
    
    function emptyCheck(){
        if($('#stock').hasClass('is-invalid')) $('#stock').removeClass('is-invalid');
        $( ".invalid-feedback" ).hide();
        $( ".invalid-feedback" ).text('');

        var ticker = $("#stock").val().toUpperCase();
        //console.log('Ticker: ' + ticker);
        
        if (!ticker || !window.stockjson) {
            if (!ticker) validateFail('Please enter in a stock ticker.');
            else if (!window.stockjson) validateFail('Database has not finished loading, wait 10 seconds then try again.');
        } else if (typeof window.stockjson[ticker] == 'undefined') {
            validateFail('Sorry - no information on this ticker in our database, check your spelling.');
        } else if (typeof window.stockjson[ticker].sector == 'undefined' || typeof window.stockjson[ticker].industry == 'undefined' || typeof window.stockjson[ticker].subindustry == 'undefined')  {
            validateFail('Sorry - no information on this ticker in our database, check your spelling.');
        } else {
            $('#stock').addClass('is-valid');
            var data = window.stockjson[ticker];
            //console.log(data);
            getETF(data);
            startSpinner();
        }
    }
    
    
    //AJAX call on load
    $.ajax({
        url: 'correlations/getstocks.php',
        dataType: 'html',
        cache: true,
        timeout: 2000,
        success: function(data){
            data = JSON.parse(data);
            //console.log (data[0]);
            $( "#stock" ).autocomplete({
                source: data[1],
                minLength: 2
            });
            window.stockjson = data[0];
        },
        error:function(){
            console.log('AJAX Error 001');
        }
    });

    
    /*
    function getSector(stock){
        var data = {};
        data.stock = stock;  //<=> data["stock"] = stock;
        $.ajax({
            url: 'correlations/getsector.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            cache: false, //Turn off caching or the AJAX returns the same value each time
            timeout: 2000,
            success: function(sectordata){
                console.log(sectordata);
                //if (sectordata.ms_sector === undefined || sectordata.ms_industry === undefined || sectordata.length == 0) {
                //    $("#maincontent").html("Ticker not found.");
                //} else {
                    getETF(sectordata);
                //}
            },
            error:function(){
                console.log('AJAX Error');
                $("#maincontent").html("Ticker not found.");
            }
        });
    }
    */
    
    function getETF(data){
        startSpinner();
        editSpinner("Pulling data...");
        
        $.ajax({
            url: 'correlations/getetfs.php',
            type: 'POST',
            data: data,
            dataType: 'html',
            cache: false,
            timeout: 2000,
            success: function(etfdata){
                //console.log("ETF Data: ");
                etfdata = JSON.parse(etfdata);
                setTimeout(function() { getHistorical(etfdata); }, 2000);
                //getHistorical(etfdata);
            },
            error:function(){
                $("#maincontent").html("Sector information not found.");
            }
        });
    }
    
    
    function getHistorical(etfinfo) {
        /*var data = {
                    stock: etfinfo.stock.type_name,
                    stock_ticker: etfinfo.stock.ticker,
                    sec: etfinfo.sector.type_name,
                    sec_ticker: etfinfo.sector.classification_code,
                    ind: etfinfo.industry.type_name,
                    ind_ticker: etfinfo.industry.classification_code
        };*/
        var data = etfinfo;
        //console.log(data);
        
        editSpinner("Running calculations...");

        $.ajax({
            url: 'correlations/gethistorical3.php',
            type: 'POST',
            data: data,
            dataType: 'html',
            cache: false,
            timeout: 10000,
            success: function(results){
                results = JSON.parse(results);
                console.log(results);
                
                editSpinner("Finalizing...");

                $(".successmsg").html('The stock <strong>' + results.index[0].lookup_codes[0] + '</strong> belongs to the industry group <strong><em>' + results.index[0].names[1] + '</em> (' + results.index[0].lookup_codes[1] + ')</strong> and the sector group <strong><em>' + results.index[1].names[1] + '</em> (' + results.index[1].lookup_codes[1] + ')</strong>.');
                makeHeatMap(results);

                makeChart(results[0],results.index[0],0);
                makeChart(results[1],results.index[1],1);
                makeChart(results[2],results.index[2],2);
                generateTable(results[0],results.index[0],0);
                generateTable(results[1],results.index[1],1);
                generateTable(results[2],results.index[2],2);
                
                endSpinner();
            },
            error:function(results){
                //console.log(results);
                validateFail('Historical data not found.');
            }
        });
        
    }
    
    function makeHeatMap(results) {
        //prep data for heatmap
        var indexdata = results.index;
        heatmapdata = [];
        indices = ['stock','industry','sector','market'];
        var k = indexdata.length;
        valuearray = [];
        for (i = 0; i < k; i++) {
            heatmapdata[i] = {};
            heatmapdata[i]['x'] = indices.indexOf(indexdata[i].types[0]);
            heatmapdata[i]['y'] = indices.indexOf(indexdata[i].types[1]);
            heatmapdata[i]['value'] = indexdata[i].lastcorrelation;
            heatmapdata[i]['name'] = indexdata[i].types[0] + '-' + indexdata[i].types[1];
            valuearray[i] = indexdata[i].lastcorrelation;

            heatmapdata[k+i] = {};
            heatmapdata[k+i]['x'] = indices.indexOf(indexdata[i].types[1]);
            heatmapdata[k+i]['y'] = indices.indexOf(indexdata[i].types[0]);
            heatmapdata[k+i]['value'] = indexdata[i].lastcorrelation;
            heatmapdata[k+i]['name'] = indexdata[i].types[0] + '-' + indexdata[i].types[1];
            
            valuearray[k+i] = indexdata[i].lastcorrelation;
        }
        
        //creates (0,0),(1,1),etc points
        var j = heatmapdata.length;
        for (i = 0; i < indices.length; i++) {
            heatmapdata[j+i] = {};
            heatmapdata[j+i]['x'] = i;
            heatmapdata[j+i]['y'] = i;
            heatmapdata[j+i]['value'] = 1;
            heatmapdata[j+i]['name'] = indices[i];
            
            valuearray[j+i] = 1;
        }
        
        //console.log(heatmapdata);
        //console.log(valuearray);
        
        var max = Math.max.apply(Math, valuearray);
        var min = Math.min.apply(Math, valuearray);
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
        
        //console.log(colorstops);
        

        
        Highcharts.chart('heatmap', {
            chart: {
                type: 'heatmap',
                height: 250,
                marginTop: 40,
                marginBottom: 40,
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
                categories: ['Stock (' + indexdata[0]['lookup_codes'][0] +')','Industry (' + indexdata[0]['lookup_codes'][1] +')','Sector (' + indexdata[1]['lookup_codes'][1] +')','Market (' + indexdata[2]['lookup_codes'][1] +')']
            },
        
            yAxis: {
                categories: ['Stock (' + indexdata[0]['lookup_codes'][0] +')','Industry (' + indexdata[0]['lookup_codes'][1] +')','Sector (' + indexdata[1]['lookup_codes'][1] +')','Market (' + indexdata[2]['lookup_codes'][1] +')'],
                title: null
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
                    return 'Current 30-day correlation between <b>' + this.series.xAxis.categories[this.point.x] + '</b> and <b>' + this.series.yAxis.categories[this.point.y] + '</b>';
                }
            },
        
            series: [{
                name: 'Correlation',
                borderWidth: 1,
                data: heatmapdata,
                dataLabels: {
                    enabled: true,
                    formatter:  function() {
                        return (this.point.value*100).toFixed(2) + '%';
                    }
                }
            }]
        
        });
    }

    function makeChart(data,info,index) {
        //console.log(info);
        //console.log(data);
        var chartid = 'chart_' + (index + 1);
        console.log(chartid);
        Highcharts.stockChart(chartid, {
            chart: {
                marginRight: 10,
                backgroundColor: 'rgba(225, 233, 240,.6)',
                plotBackgroundColor: '#FFFFFF',
                plotBorderColor: '#C0C0C0',
                //plotBorderWidth: 1,
                height:500
            },
            title: {
                text: ucFirst(info.names[0]) + ' - rolling correlation to its ' + info.types[1] + ' group (' + info.names[1] + ')'
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
                    /*{
                        type: 'day',
                        count: ytdcount,
                        text: '2016 YTD'
                    },*/
                    {
                        type: 'year',
                        count: 1,
                        text: '1y'
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
            yAxis: [{
                title: {
                    text: 'Rolling 30-day Correlation',
                },
                height: '70%',
                linewidth: 5,
                minorGridLineWidth: 0,
                plotLines : [{
                    value : 0,
                    color : 'black',
                    width : 2,
                    zIndex: 5
                }],
                opposite: false,
                min: -1,
                max: 1
            },{
                title: {
                    text: "Return",
                },
                top: '85%',
                height: '15%',
                offset: 0,
                lineWidth: 2,
                minorGridLineWidth: 0,
                tickInterval: 50,
                plotLines : [{
                        value : 100,
                        color : Highcharts.getOptions().colors[1],
                        width : 1,
                        zIndex: 5
                    },{
                        value : 0,
                        color : Highcharts.getOptions().colors[1],
                        width : 1,
                        zIndex: 5
                    }],
                opposite: false,
                labels: {
                    enabled: true,
                    style: {
                        color: 'rgba(0,0,0,0)', //clears labels - keeps labels offsetiung ability, hides labels themselves
                    }
                }
            }
            ],
            legend: {
                enabled: false
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
                }
            },
            series: [{
                name: info.types[0].charAt(0).toUpperCase() + info.types[0].slice(1) + ' Return',
                color : 'darkblue',
                shadow : true,
                type: 'line',
                yAxis: 1,
                compare: 'percent',
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.change}%</b><br/>',
                    changeDecimals: 2,
                    valueDecimals: 2
                },
                data: data.json_data1
            },{
                name: 'Sector Return',
                color : 'darkgreen',
                type: 'line',
                yAxis: 1,
                compare: 'percent',
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.change}%</b><br/>',
                    changeDecimals: 2,
                    valueDecimals: 2
                },
                data: data.json_data2
            },{
                name: 'Correlation',
                color: 'darkred',
                shadow : true,
                type: 'line',
                yAxis: 0,
                //compare: 'percent',
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                    changeDecimals: 2,
                    valueDecimals: 2
                },
                data: data.json_correlation
            }
            ]
        });
    }
    
    function generateTable(data,info,index) {
        //creates datatable html following the chart
        var chartid = 'chart_' + (index + 1);
        var tableid = 'table_' + (index + 1);
        
        $( '#' + chartid ).after(function() {
            return '<table id="' + tableid + '">'  + "</table>";
        });

        //formats the data so that datatables can use it easily
        var keys = [], i = 0; for (keys[i++] in data.csv_data) {}
        
        prepped_data = [];
        for (i = 0; i < keys.length; i++) {
            var datekey = keys[i];

            prepped_data[i] = [];
            
            prepped_data[i][0] = data.csv_data[datekey]['date'];
            
            if (typeof data.csv_data[datekey]['data1_price'] != 'undefined') prepped_data[i][1] = data.csv_data[datekey]['data1_price'].toFixed(2);
            else prepped_data[i][1] = 'N/A';

            if (typeof data.csv_data[datekey]['data1_roi'] != 'undefined') prepped_data[i][2] = (data.csv_data[datekey]['data1_roi']*100).toFixed(2) + '%';
            else prepped_data[i][2] = 'N/A';
            
            if (typeof data.csv_data[datekey]['data2_price'] != 'undefined') prepped_data[i][3] = data.csv_data[datekey]['data2_price'].toFixed(2);
            else prepped_data[i][3] = 'N/A';

            if (typeof data.csv_data[datekey]['data2_roi'] != 'undefined') prepped_data[i][4] = (data.csv_data[datekey]['data2_roi']*100).toFixed(2) + '%';
            else prepped_data[i][4] = 'N/A';

            if (typeof data.csv_data[datekey]['correlation'] != 'undefined') prepped_data[i][5] = (data.csv_data[datekey]['correlation']*100).toFixed(2) + '%';
            else prepped_data[i][5] = 'N/A';

        }

        
        
        $('#' + tableid).DataTable({
            dom: "<'row'<'col-sm-12'B>>" +
                "<'row'<'col-sm-12'i>>" +

                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12'p>>",

            //dom: 'Brt<"bottom"ip>',
            "data" : prepped_data,
            "searching": false,
            "buttons": ['copy', 'csv', 'excel'],
            "order": [[ 0, "desc" ]],
            "columns": [
                { "title" : "Date"},
                { "title" : info.lookup_codes[0] + " price"},
                { "title" : info.lookup_codes[0] + " % daily change"},
                { "title" : info.lookup_codes[1] + " price"},
                { "title" : info.lookup_codes[1] + " % daily change"},
                { "title" : "Correlation" }
            ],
            "pageLength": 30,
            "language": {
                "info": "<b>_TOTAL_</b> total observations",
            }
        });

        //Add "download data" message
        var tablewrapperid = tableid + '_wrapper';
        $( '#' + tablewrapperid + ' ' + 'div.dt-buttons' ).prepend('<span style="font-weight:bold">Download data: </span>');
        
        //Add upper row
        var trstring = '<tr><td class="upperrow"></td> <td class="upperrow "colspan="2"> <span>' + ucFirst(info.types[0]) +': ' + ucFirst(info.names[0]) + ' (' + info.lookup_codes[0]  + ')</span></td><td class="upperrow" colspan="2"><span>'+ ucFirst(info.types[1])+': '+ ucFirst(info.names[1]) + ' (' + info.lookup_codes[1]  + ')</span></td><td class="upperrow"></td><tr>';
        
        $( '#' + tableid +' thead' ).prepend(trstring);
        
        $( '#' + tableid ).append('<tfoot><tr><td colspan="6"></td></tr></tfoot>');

    }
    
    
    
}); //document.ready