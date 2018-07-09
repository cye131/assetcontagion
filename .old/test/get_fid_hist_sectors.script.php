<head></head>
<body>
    <ul>
        <li>Use ?l=1 (or anything set) for data going back to 1980 instead of last Monday</li>
        <li>Use ?limit for each row of 10, limit=0 for all rows (don't use limit=0&l=1, insufficient memory)</li>
    </ul>
<?php

    require_once(dirname(dirname(__FILE__)).'/../config/db.php'); //Single Nest: include(dirname(__FILE__). '/../config/db.php'); //Double Nest: require_once(dirname(dirname(__FILE__)).'/../config/db.php');
    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    $stmt = $conn->prepare("SELECT id,sector_name,classification_level,classification_code FROM `fid_tickers_sectors` WHERE classification_code IS NOT NULL ORDER by `classification_code` ASC");
    $stmt->execute();
    
    //https://stackoverflow.com/questions/18753262/example-of-how-to-use-bind-result-vs-get-result
    $stmt->bind_result($id,$sector_name,$classification_level,$classification_code);
    
    $array = array();
    $i = (int) 0;
    $urlstr = array();
    $strstr = (string) "";
    
    while ($stmt->fetch()) {
        if (!is_null($classification_code)) {
            $array[$classification_code] = array();
            $array[$classification_code]['id'] = $id;
            $array[$classification_code]['sector_name'] = $sector_name;
            $array[$classification_code]['classification_level'] = $classification_level;
            
            if ( ($i%10) === 0 && $i!== 0) {//every 10th row, create a new $urlstr subarray
                $urlstr[] = substr($strstr,1);
                $strstr = "";
            }
            
            $strstr = $strstr.','.$classification_code;
            $i++;
        }
    }
    
    $urlstr[] = substr($strstr,1); //add final $urlstr subarray

    
    $stmt->free_result();
    $stmt->close();
        
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 0);
    $headers = array(
        'Host: fastquote.fidelity.com',
        'Referer: https://eresearch.fidelity.com/eresearch/markets_sectors/sectors/sectors_in_market.jhtml',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36'
    );
    
    $j = (int) 0;
    if (isset($_GET['limit'])) $limit = (int) $_GET['limit'];
    else exit;
    
    echo 'LIMIT: '.$limit;
    foreach ($urlstr as $urlpiece) {
        $j++;
        
        if ($j != $limit && $limit !== 0) continue;
        
        if (isset($_GET['l'])) $lastmonday = '1980/01/01';
        else $lastmonday = date('Y/m/d',strtotime("-2 weeks"));
        
        $url = 'https://fastquote.fidelity.com/service/historical/chart/lite/json?productid=research&symbols='.$urlpiece.'&dateMin='.$lastmonday.':00:00:00&dateMax='.date('Y/m/d').':00:00:00&intraday=n&granularity=1&incextendedhours=n&dd=y';
        echo htmlspecialchars($url).'</br>';
        curl_setopt($ch, CURLOPT_URL, $url);
        
        $html = curl_exec($ch);
        
        $clean = substr($html,0,-2); //remove opening and closing ( in data
        $clean = substr($clean,2);
        $json = json_decode($clean,true);
        $json = $json['SYMBOL'];
        $identifier = (string) "";
        foreach ($json as $row) {            
            $identifier = $row['IDENTIFIER'];
            if (!isset($row['DESCRIPTION'])) {
                $array[$identifier]['status'] = (boolean) 0;
            } else {
                echo $row['IDENTIFIER'].'<br>';
                $array[$identifier]['status'] = (boolean) 1;
                $array[$identifier]['description'] = $row['DESCRIPTION'];
                $array[$identifier]['data'] = array();
                
                if (!isset($row['BARS']['CB'])) continue;
                foreach ($row['BARS']['CB'] as $k => $point) {
                    $timestamp = new DateTime();
                    $timestamp = DateTime::createFromFormat('!m-d-Y::H:i:s', $point['lt'])->getTimestamp();
                    $datestr = (string) date('Y-m-d',$timestamp);
                    $array[$identifier]['data'][$datestr] = array();
                    $array[$identifier]['data'][$datestr]['close'] = (float) $point['cl'];
                    
                    if ($k > 0) $array[$identifier]['data'][$datestr]['roi'] = (float) round(($point['cl']-$lastprice)/$lastprice,4);
                    else $array[$identifier]['data'][$datestr]['roi'] = (float) 0;
                    $lastprice = (float) $point['cl']; 
                }
            }
        }
        
    }
    
    $historicaldata = array();
    $i = 0;
    foreach ($array as $id => $row) {
        $classification_id = (string) $id;
        if (!isset($row['data'])) continue;
        
        
        foreach ($row['data'] as $date => $point) {
            $historicaldata[$i]['date'] = $date;
            $historicaldata[$i]['roi'] = $point['roi'];
            $historicaldata[$i]['close'] = $point['close'];
            
            $historicaldata[$i]['classification_id'] = $classification_id;
            $historicaldata[$i]['id'] = str_replace('-','',$date).$classification_id;
            $historicaldata[$i]['fid_tickers_sectors_id'] = $row['id'];
            
            $i++;
        }               
    }
    
    
    $sql = array(); 
    foreach( $historicaldata as $row ) {
        $sql[] = "('".(mysqli_real_escape_string($conn,$row['id']))
        ."', '".mysqli_real_escape_string($conn,$row['classification_id'])
        ."', '".mysqli_real_escape_string($conn,$row['date'])
        ."', '".mysqli_real_escape_string($conn,$row['close'])
        ."', '".mysqli_real_escape_string($conn,$row['roi'])
        ."', '".mysqli_real_escape_string($conn,$row['fid_tickers_sectors_id'])."')";
    }
    $query = "REPLACE INTO `fid_hist_sectors` (id,classification_id,date,close,roi,fid_tickers_sectors_id) VALUES ".implode(',', $sql);
    
    if ($conn->query($query)) {
        printf("%d Row inserted.\n", $conn->affected_rows);
    }
    
    $conn->close();
    echo "<pre>";
    print_r($urlstr);
    echo '<hr>';
    //print_r($array);
    //print_r($array['.GSPBIOT']);
    //print_r($historicaldata);
    echo "</pre>";
    //echo $query;
?>
</body>