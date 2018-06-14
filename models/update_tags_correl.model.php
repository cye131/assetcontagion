<?php

$seriesByFreq = array();
foreach ($tagsSeries as $row) {
    $seriesByFreq[$row['freq']][$row['fk_id']] = $row;
}

$trail = ['d' => [30,60],
            'w' => [52],
            'm' => [24],
            'q' => [12],
            'a' => [10]
            ];

$existingSCorrNid = array_column($tagsCorrel,'s_corr_nid');
//print_r($existingSCorrNid);
//ALWAYS ORDER BY B_ID
$sqldata = array();
//print_r($seriesByFreq);
foreach ($seriesByFreq as $freq => $rows) {
        //if ($freq !== 'd') continue;
        
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
            //Skip if already in existing $tagsCorrel data
            if (in_array("{$row1['category']}.{$row1['s_id']}.{$row2['s_id']}.$freq.$tr",$existingSCorrNid) ) continue;

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
        
        //print_r($sqldata);
        //(new TestOutput($sqldata) )-> print();
    
}

//exit();
if ( count($sqldata) > 0) {
  $colnames = array('s_corr_nid','category','fk_id_1','fk_id_2','freq','trail','last_updated');
  $sql -> multipleInsert('tags_correl',$colnames,$sqldata);
  
  $modeldata['script'] = 'data='.json_encode($sqldata).';';
}
else {
  $modeldata['script'] = 'data="No data";';
}

