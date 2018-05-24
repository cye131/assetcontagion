<head></head>
<body>
<?php

    require_once(dirname(dirname(__FILE__)).'/../config/db.php'); //Single Nest: include(dirname(__FILE__). '/../config/db.php'); //Double Nest: require_once(dirname(dirname(__FILE__)).'/../config/db.php');
    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT sector_name,classification_level,classification_code FROM `sector_sectoretfmatch` ORDER by `classification_code` ASC");
    $stmt->execute();
    
    //https://stackoverflow.com/questions/18753262/example-of-how-to-use-bind-result-vs-get-result
    $stmt->bind_result($sector_name,$classification_level,$classification_code);
    
    $array = array();
    $i = (int) 0;
    $urlstr = array();
    $strstr = (string) "";
    
    while ($stmt->fetch()) {
        if (!is_null($classification_code)) {
            $array[$classification_code] = array();
            $array[$classification_code]['sector_name'] = $sector_name;
            $array[$classification_code]['classification_level'] = $classification_level;
            
            if ( ($i%10) === 0 && $i!== 0) {//every 10th row, restart array str and create a near $urlstr subarray
                $urlstr[] = substr($strstr,1);
                $strstr = "";
            }
            $strstr = $strstr.','.$classification_code;
            $i++;
        }
    }
    
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
    $limit = (int) $_GET['limit'];
    echo 'LIMIT: '.$limit;

    foreach ($urlstr as $urlpiece) {
        $j++;
        if ($j != $limit) continue;
        
        $url = 'https://fastquote.fidelity.com/service/historical/chart/lite/json?productid=research&symbols='.$urlpiece.',.SPX&dateMin=2000/01/01:00:00:00&dateMax='.date('Y-m-d').':00:00:00&intraday=n&granularity=1&incextendedhours=n&dd=y';
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
        if ($id !== '.SPX') $classification_id = (string) 'gics_'.$id;
        else $classification_id = (string) $id;
        if (!isset($row['data'])) continue;
        
        
        foreach ($row['data'] as $date => $point) {
            $historicaldata[$i]['date'] = $date;
            $historicaldata[$i]['roi'] = $point['roi'];
            $historicaldata[$i]['close'] = $point['close'];

            
            $historicaldata[$i]['classification_id'] = $classification_id;
            $historicaldata[$i]['id'] = str_replace('-','',$date).$classification_id;

            $i++;
        }               
    }
    
    
    $sql = array(); 
    foreach( $historicaldata as $row ) {
        $sql[] = "('".(mysqli_real_escape_string($conn,$row['id']))
        ."', '".mysqli_real_escape_string($conn,$row['classification_id'])
        ."', '".mysqli_real_escape_string($conn,$row['date'])
        ."', '".mysqli_real_escape_string($conn,$row['close'])
        ."', '".mysqli_real_escape_string($conn,$row['roi'])."')";
    }

    $query = "REPLACE INTO `sector_indexhistoricaldata` (id,classification_id,date,close,roi) VALUES ".implode(',', $sql);
    
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