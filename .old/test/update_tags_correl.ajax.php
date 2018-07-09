<?php
if (!function_exists('myAutoloader')) {
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    spl_autoload_register('myAutoloader');
}
$sql = new MyPDO();
//ini_set('post_max_size', '60M'); ini_set('upload_max_filesize', '60M'); ini_set('max_input_time', '999');ini_set('max_input_nesting_level', '999');ini_set('memory_limit', '1024M');ini_set('max_input_vars', '9999');

require_once('get_tags_series.script.php');
$series = $tagsSeries;
//$series = $_POST['ajax'] ?? NULL;
$ajax = array('data' => array(),'info' => array());
//print_r($series);

$seriesByFreq = array();
foreach ($series as $row) {
    $seriesByFreq[$row['freq']][$row['fk_id']] = $row;
}

$trail = ['d' => [30,60],
            'w' => [52],
            'm' => [12],
            'q' => [8],
            'a' => [10]
            ];

//ALWAYS ORDER BY B_ID
$sqldata = array();
//print_r($seriesByFreq);
foreach ($seriesByFreq as $freq => $rows) {
        if ($freq !== 'd') continue;
            
        $codes = array_column($rows,'code');
        $keys = array_column($rows,'b_id');
        $codesArray = array_combine($keys,$codes);
        ksort($codesArray);
        $codesArray = array_values($codesArray);

        $doubles = (array) CorrelationData::getDupletCombinations($codesArray); //Preserves alphabetical ordering

        foreach ($doubles as $double) {
            
            $double = (array) $double;
            $code1 = $double[0];
            $code2 = $double[1];

            foreach ($rows as $row) {
                if ($code1 == $row['code']) $row1 = $row;
                if ($code2 == $row['code']) $row2 = $row;
            }
            //$row1 = $rows[array_search($code1,$codesArray)];
            //$row2 = $rows[array_search($code2,$codesArray)];
            
            foreach ($trail[$freq] as $tr) {
                $sqldata[] = array(
                                    's_corr_nid' => "{$row1['category']}.{$row1['s_id']}.{$row2['s_id']}.$freq.$tr",
                                    'category' => $row1['category'],
                                    'fk_id_1' => $row1['s_id'],
                                    'fk_id_2' => $row2['s_id'],
                                    'freq' => $freq,
                                    'trail' => $tr,
                                    'last_updated' => date('Y-m-d H:i:s')
                                  );
                //print_r($row1);print_r($row2);
            }
        }
        
        
        print_r($sqldata);
    
}

exit();
if ( count($sqldata) > 0) {

  $colnames = array('s_corr_nid','category','fk_id_1','fk_id_2','freq','trail','last_updated');
  $sql -> multipleInsert('tags_correl',$colnames,$sqldata);
  
}

