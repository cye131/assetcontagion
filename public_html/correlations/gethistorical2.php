<?php
    require_once('functions.php');
    
    function exitMsg($message){
        //echo message;
        exit();
    }

    
    //{ stock: "Apple Inc.", stock_ticker: "AAPL", sec: "Technology", sec_ticker: "XLK", ind: "Computer Hardware", ind_ticker: "XTH" }
    $arr = array();
    foreach($_POST as $k => $v){
        $arr[$k] = $v;
    }
    
    //print_r($arr);
    $url = "https://api.iextrading.com/1.0/stock/market/batch?symbols=".$arr['stock_ticker'].",".$arr['sec_ticker'].",".$arr['ind_ticker'].",SPY&types=chart&range=5y";

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $json = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($json, true);
    if (!isset($data)) exit();
    
    
    
    
    //Create array that will be used for handling data cleaning
    $portfolio = array();
    $pairs = array('stock'=>$arr['stock_ticker'],'industry'=>$arr['ind_ticker'],'sector'=>$arr['sec_ticker'],'market'=>'SPY');
    $fnames = array('stock'=>$arr['stock'],'industry'=>$arr['ind'],'sector'=>$arr['sec'],'market'=>'S&P 500');

    function addToFinalArray($namesarray) {
        global $portfolio;
        global $data;
        global $pairs;
        
        asort($namesarray);
        $namestring = (string) implode('-',$namesarray);
        $portfolio[$namestring] = array();
        $portfolio[$namestring]['historicaldata'] = array();
        
        
        //Gets the first shared date between all elements in $namesarray, and the 30th data point after that
        $firstdates = array();
        $thirtiethdates = array();
        foreach ($namesarray as $name) {
            $firstdates[] = strtotime($data[$pairs[$name]]['chart'][0]['date']);
            $thirtiethdates[] = strtotime($data[$pairs[$name]]['chart'][29]['date']);
        }
        $portfolio[$namestring]['firstshareddate'] = max($firstdates);
        $portfolio[$namestring]['thirtiethshareddate'] = max($thirtiethdates);

        
        $correlation_counter = array();

        //Moves historical data from $data array into main array
        foreach ($namesarray as $name) {
            $returnname = (string) $name.'_return';
            foreach ($data[$pairs[$name]]['chart'] as $row) {
                $date = $row['date'];
                if (strtotime($date) < $portfolio[$namestring]['firstshareddate']) continue;

                if (isset($row['close'])) $portfolio[$namestring]['historicaldata'][$date]['timestamp'] = strtotime($date)*1000; //JS in milliseconds

                if (isset($row['close'])) $portfolio[$namestring]['historicaldata'][$date][$name] = $row['close'];
                if (isset($row['changePercent'])) $portfolio[$namestring]['historicaldata'][$date][$returnname] = $row['changePercent'];
            }
            
            $cname = (string) $name.'_last30';
            $correlation_counter[$cname] = array();
        }
        
        //last 30 days to array for later correlation calculations - don't add unless both data points exist
        foreach ($portfolio[$namestring]['historicaldata'] as $date => $row) {            
            $bothexist = (boolean) TRUE;
            foreach ($namesarray as $name) {
                if (!isset($row[$name])) $bothexist = FALSE;
                $portfolio[$namestring]['historicaldata'][$date]['bothexist'] = $bothexist;//debug                
            }
            //Skip to next loop if both data points don't exist
            if ($bothexist === FALSE) continue;
            
            $bothhave30 = (boolean) TRUE;
            //Add newest data point to list of 30
            foreach ($namesarray as $name) {
                $returnname = (string) $name.'_return';
                $cname = (string) $name.'_last30';

                array_push($correlation_counter[$cname],$row[$returnname]);
                
                //If >30 data points, strip one.
                if  (count($correlation_counter[$cname]) > 30) {
                    array_shift($correlation_counter[$cname]);    
                }
                
                if (count($correlation_counter[$cname]) !== 30) $bothhave30 = FALSE; //Check that both datasets have 30 variables exactly

                $portfolio[$namestring]['historicaldata'][$date][$cname] = implode(',',$correlation_counter[$cname]);
            }
            
            //Calculate correlation
            if ($bothhave30 === TRUE && count($correlation_counter) == 2) {
                $keys = array_keys($correlation_counter);
                $portfolio[$namestring]['historicaldata'][$date]['correlation'] = correlation($correlation_counter[$keys[0]],$correlation_counter[$keys[1]]);
            }
        }
        
        ksort($portfolio[$namestring]['historicaldata']);
        return $portfolio;
    }
    
    addToFinalArray(array('stock','industry'));
    addToFinalArray(array('stock','sector'));
    addToFinalArray(array('stock','market'));
    
    //addToFinalArray(array('stock','market','sector','industry'));

    //print_r($portfolio);
    //echo json_encode($portfolio);
    
    $json = array();
    //Create information array
    $json['info'] = array();

    function prepJSON($namesarray) {
        global $portfolio;
        global $data;
        global $json;
        global $pairs;
        global $fnames;
        
        
        asort($namesarray);
        $namestring = (string) implode('-',$namesarray);
        
        
        //Create correlation and place it json.industry_stock.0 <<<- 0 = correlation!
        
        $jsonname= str_replace("-","_",$namestring);
        $json[$jsonname] = array();

        $json['info'][$jsonname] = array();
        
        $i = (int) 0;
        
        foreach ($portfolio[$namestring]['historicaldata'] as $date => $row) {
            if (!isset($row['correlation'])) continue;
            $json[$jsonname][0][$i] = array();
            $json[$jsonname][0][$i][0] = $row['timestamp'];
            $json[$jsonname][0][$i][1] = (float) number_format($row['correlation']*100,2);
            
            $i++;
        }
        
        //Create for individual tickers and name it json.industry_stock.1 (for industry) and json.industry_stock.2 (for stock) <<<- alphabetical order
        $m = (int) 1;
        foreach ($namesarray as $name) {
            $json[$jsonname][$m] = array();
            
            $j = (int) 0;
            
            foreach ($portfolio[$namestring]['historicaldata'] as $date => $row) {
                if (!isset($row[$name])) continue;
                $json[$jsonname][$m][$j] = array();
                $json[$jsonname][$m][$j][0] = $row['timestamp'];
                $json[$jsonname][$m][$j][1] = $row[$name];
                
                $j++;
            }
            
            $indexname = (string) $m.'_name';
            $indexticker = (string) $m.'_ticker';
            $indexfname = (string) $m.'_fname';

            $json['info'][$jsonname][$indexname] = $name;
            $json['info'][$jsonname][$indexticker] = $pairs[$name];
            $json['info'][$jsonname][$indexfname] = $fnames[$name];

            $m++;
        }
        

        return $json;
    }
    
    prepJSON(array('stock','industry'));
    prepJSON(array('stock','sector'));
    prepJSON(array('stock','market'));
    
    
    //echo '<pre>';
    //print_r($portfolio);
    //echo '</pre>';
    echo json_encode($json);
    

    
    //file_put_contents('test.json',$json);    
?>