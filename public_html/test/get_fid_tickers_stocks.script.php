<head></head>
<body>
<?php
    require_once(dirname(dirname(__FILE__)).'/../config/db.php'); //Single Nest: include(dirname(__FILE__). '/../config/db.php'); //Double Nest: require_once(dirname(dirname(__FILE__)).'/../config/db.php');
    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    $ch = curl_init();
    
    $url = 'https://research2.fidelity.com/pi/stock-screener/LoadResults';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $i = (int) 0;
    $array = array();
    while ($i<=5700) {
        echo '<hr><strong>'.$i.'</strong>';
        $j = $i + 100;
        
        curl_setopt($ch, CURLOPT_POSTFIELDS,'
                    AjaxId=2&Criteria=%5B%7B%22ArgsOperator%22%3A%22OR%22%2C%22Arguments%22%3A%5B%7B%22ArgsOperator%22%3Anull%2C%22Arguments%22%3A%5B%5D%2C%22Clauses%22%3A%5B%7B%22Operator%22%3A%22Equals%22%2C%22Values%22%3A%5B%2210%22%2C%2215%22%2C%2220%22%2C%2225%22%2C%2230%22%2C%2235%22%2C%2240%22%2C%2245%22%2C%2250%22%2C%2255%22%2C%2260%22%5D%7D%5D%2C%22ClauseGroups%22%3A%5B%5D%2C%22Field%22%3A%22FI.SectorGicsCode%22%7D%5D%2C%22Identifiers%22%3A%5B%22%7B%5C%22id%5C%22%3A%5C%22FI.SectorGicsCode%5C%22%7D%22%5D%7D%5D&ResultView=SearchCriteria&FirstRow='.$i.'&RowCount=100&SortDir=A&SortField=ticker&SortResults=true&InitialLoad=false&ScreenerId=128
                    ');
                    
        $json = curl_exec($ch);
    
        $clean = json_decode($json,true);
        //print_r($clean);
        $html = $clean['html'];
        //echo $html;
    
        
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        
        //Get tables ticker
        $table1 = $dom->getElementsByTagName('table')->item(0);
        //print_r($table1);
        $trows1 = $table1->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
        //print_r($trows1);
        $n = $i;
        foreach ($trows1 as $row) {
                $array[$n]['ticker'] = (string) $row->getElementsByTagName('td')->item(1)->getElementsByTagName('div')->item(0)->getElementsByTagName('a')->item(0)->nodeValue;
                $n++;
        }
        
        //Get data ticker
        $table2 = $dom->getElementsByTagName('table')->item(1);
        $trows2 = $table2->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
        //print_r($trows2);
        $m = $i;
        
        foreach ($trows2 as $row) {
                if (!isset($array[$m]['ticker'])) continue;
                
                $array[$m]['fy_name'] = (string) $row->getElementsByTagName('td')->item(0)->getElementsByTagName('div')->item(0)->nodeValue;
                //echo  $array[$m]['name'].'<br>';
                $array[$m]['gics_sector'] = (string) $row->getElementsByTagName('td')->item(3)->getElementsByTagName('span')->item(0)->nodeValue;
                $array[$m]['gics_industry'] = (string) $row->getElementsByTagName('td')->item(4)->getElementsByTagName('span')->item(0)->nodeValue;
                $array[$m]['gics_subindustry'] = (string) $row->getElementsByTagName('td')->item(5)->getElementsByTagName('span')->item(0)->nodeValue;                
                
                $array[$m]['id'] = $array[$m]['ticker'].'-gics';
                $m++;
        }
        
        
        $i = $i + 100;
    }
    
    curl_close($ch);
    
    $date = date('Y-m-d');
    
    $sql = array(); 
    foreach( $array as $row ) {
        $sql[] = "('".mysqli_real_escape_string($conn,$row['id'])
        ."', '".mysqli_real_escape_string($conn,$row['ticker'])
        ."', '".mysqli_real_escape_string($conn,'gics')
        ."', '".mysqli_real_escape_string($conn,$row['gics_sector'])
        ."', '".mysqli_real_escape_string($conn,$row['gics_industry'])
        ."', '".mysqli_real_escape_string($conn,$row['gics_subindustry'])
        ."', '".mysqli_real_escape_string($conn,$row['fy_name'])
        ."', '".mysqli_real_escape_string($conn,$date)."')";
    }
    $query = "REPLACE INTO `sector_stocksectormatch` (id,ticker,classification_type,sector,industry,subindustry,name,lastupdated) VALUES ".implode(',', $sql);
    if ($conn->query($query)) {
        printf("%d Row inserted.\n", $conn->affected_rows);
    }
    //echo $query;
?>
</body>

<pre>
    
    <?php print_r($array);?>
</pre>