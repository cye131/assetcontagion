<?php
    require_once('functions.php');
    
    function exitMsg($message){
        //echo message;
        exit();
    }
    
    //{"stock":"AAPL","sec":"Technology","sec_ticker":"XLK","ind":"Computer Hardware","ind_ticker":"XTH"}
    $arr = array();
    foreach($_POST as $k => $v){
        $arr[$k] = $v;
    }
    
    //print_r($arr);
    $url = "https://api.iextrading.com/1.0/stock/market/batch?symbols=".$arr['stock'].",".$arr['sec_ticker'].",".$arr['ind_ticker'].",SPY&types=chart&range=5y";

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $json = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($json, true);
    if (!isset($data)) exit();
    
    
    
    
    
    //First shared index (pairs: stock-sector,sector-industry,stock-market,stock-sector-industry-market)    
    $minindexcount = min(
        count($data[$arr['stock']]['chart']),
        count($data[$arr['sec_ticker']]['chart']),
        count($data[$arr['ind_ticker']]['chart'])
    );
    
    function firstSharedDate($data,$name1,$name2,$i) {
        return date("Y-m-d",max(
            strtotime($data[$name1]['chart'][$i]['date']),
            strtotime($data[$name2]['chart'][$i]['date'])
        ));
    }
    
    $ssfirstdate = firstSharedDate($data,$arr['stock'],$arr['sec_ticker'],0);
    $sifirstdate = firstSharedDate($data,$arr['stock'],$arr['ind_ticker'],0);
    $ss30date = firstSharedDate($data,$arr['stock'],$arr['sec_ticker'],29);
    $si30date = firstSharedDate($data,$arr['stock'],$arr['ind_ticker'],29);

    
    //echo $minindexcount.'|';
    //echo $ssfirstdate;
    //echo $sifirstdate; echo $si30date;


    if ($minindexcount < 40) exitMsg("Not enough observations.");
    
    
    //Create final array with dates as keys to second-level arrays
    //Note - Sector-ssv would mean the sector ETF's value, starting at $100 on the first day where data was recorded for both the stock and the sector
    //Sector-siv and Industry-ssv are meaningless!
    function portfolioMake($data,$type,$typename) {
        $pf = array();
        $retname = $typename."-r";
        
        global $ssfirstdate;
        global $sifirstdate;
        
        $ssval = $typename."-ssv";
        $sival = $typename."-siv";
        
        $ss = 0;
        $si = 0;
        
        foreach ($data[$type]['chart'] as $row) {
            $date = $row['date'];
            $pf[$date] = array();
            $pf[$date][$typename] = $row['close'];
            $pf[$date][$retname] = $row['changePercent'];
            
            if (strtotime($date) >= strtotime($ssfirstdate)) {
                if ($ss == 0) $ss = 100;
                else $ss = number_format(($row['changePercent']/100 + 1)*$ss,2);
                $pf[$date][$ssval] = $ss;
            }
            
            if (strtotime($date) >= strtotime($sifirstdate)) {
                if ($si == 0) $si = 100;
                else $si = number_format(($row['changePercent']/100 + 1)*$si,2);
                $pf[$date][$sival] = $si;
            }
        }
        return $pf;
    }
    
    $portfolio = array_merge_recursive(
                portfolioMake($data,$arr['stock'],"Stock"),
                portfolioMake($data,$arr['sec_ticker'],"Sector"),
                portfolioMake($data,$arr['ind_ticker'],"Industry")
            );
    
    //Correlation Calculations
    $ss_s_correl_array = array();
    $ss_sec_correl_array = array();
    
    $si_s_correl_array = array();
    $si_ind_correl_array = array();

    foreach ($portfolio as $k => $row) {
        $date = $k;

        if ((strtotime($date) >= strtotime ($ss30date)) && isset($row['Stock-r']) && isset($row['Sector-r']) ) { //If already 30 obs stored
            array_shift($ss_s_correl_array);
            array_push($ss_s_correl_array,$row['Stock-r']);
            array_shift($ss_sec_correl_array);
            array_push($ss_sec_correl_array,$row['Sector-r']);
            
            $portfolio[$date]['ss_s_correl_str'] = implode(",",$ss_s_correl_array);
            $portfolio[$date]['ss_se_correl_str'] = implode(",",$ss_sec_correl_array);

            $portfolio[$date]['ss_correl'] = correlation($ss_s_correl_array,$ss_sec_correl_array);
        } elseif ((strtotime($date) >= strtotime ($ssfirstdate)) && isset($row['Stock-r']) && isset($row['Sector-r']) ){ //If not already 30 obs stored
            $ss_s_correl_array[] = $row['Stock-r'];
            $ss_sec_correl_array[] = $row['Sector-r'];
        }
        
        
        
        if ((strtotime($date) >= strtotime ($si30date)) && isset($row['Stock-r']) && isset($row['Industry-r']) ) { //If already 30 obs stored
            array_shift($si_s_correl_array);
            array_push($si_s_correl_array,$row['Stock-r']);
            array_shift($si_ind_correl_array);
            array_push($si_ind_correl_array,$row['Industry-r']);
            
            $portfolio[$date]['si_s_correl_str'] = implode(",",$si_s_correl_array);
            $portfolio[$date]['si_ind_correl_str'] = implode(",",$si_ind_correl_array);

            $portfolio[$date]['si_correl'] = correlation($si_s_correl_array,$si_ind_correl_array);
        } elseif ((strtotime($date) >= strtotime ($sifirstdate)) && isset($row['Stock-r']) && isset($row['Industry-r']) ){ //If not already 30 obs stored
            $si_s_correl_array[] = $row['Stock-r'];
            $si_ind_correl_array[] = $row['Industry-r'];
        }

    
        
    }
    
    $array = array();
    $i = 0;
    $j = 0;
    foreach($portfolio as $k=>$row){   //LOOPS TO GET UTC TIME SERIES
        
        if (isset($row['Stock-ssv'])) {
            $array['stockss'][$i][0] = strtotime($k) * 1000; //Javascript needs microtime instead of seconds
            $array['stockss'][$i][1] = (float)$row['Stock-ssv'];
        }
        
        if (isset($row['Sector-ssv'])) {
            $array['sectorss'][$i][0] = strtotime($k) * 1000;
            $array['sectorss'][$i][1] = (float)$row['Sector-ssv'];
        }
        if (isset($row['ss_correl'])) {
            $array['correlss'][$j][0] = strtotime($k) * 1000;
            $array['correlss'][$j][1] = (float)$row['ss_correl']*100;
            $j++;
        }
        $i++;
    }
    //$res['chartstr'] = rtrim($var1, ",");

    $json = json_encode($array);
    echo $json;
    
    //file_put_contents('test.json',$json);    
?>