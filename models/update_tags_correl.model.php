<?php

$tagsSeries = $fromAjax['tagsSeries'] ?? $fromRouter['tagsSeries'] ?? NULL;
$specsCategories = $fromAjax['specsCategories'] ?? $fromRouter['specsCategories'] ?? NULL;
$existingUniqIdentifiers = $fromAjax['existingUniqIdentifiers'] ?? $fromRouter['existingUniqIdentifiers'] ?? [];
unset($fromAjax);
//$specsCategories should be an array corresponding to only ONE category --> Note, there are a LOT of variables here so make sure max_input_vars is high enough in php.ini (currently working with 5000)
//$tagsCorrel and $tagsSeries are assumed to be already filtered correctly


//Don't trust any natural keys within the table, calculate instead
/*$existingUniqIdentifiers = [];
foreach ($tagsCorrel as $row) {
  $existingUniqIdentifiers[] = $row['category']. '.' .$row['fk_id_1']. '.' .$row['fk_id_2']. '.' .$row['freq']. '.' .$row['trail'];
}*/


//Trail-freq info
$trailTmp = explode(',',$specsCategories['cat_freqtrails']);
$trail = [];
foreach ($trailTmp as $t) {
  $trail[substr($t,0,1)][] = substr($t,2);
}
$corrTypes = explode(',',$specsCategories['cat_corrtypes']);


$seriesByFreq = [];
foreach ($tagsSeries as $row) {
    $seriesByFreq[$row['freq']][$row['fk_id']] = $row;    
}


//ALWAYS ORDER BY B_ID
$sqldata = array(); $uTagsCorr = array();
//print_r($seriesByFreq);
foreach ($seriesByFreq as $freq => $rows) {
          
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
            foreach ($corrTypes as $corrtype) {
              
              //Skip if already in existing $tagsCorrel data
              $s_corr_nid = "{$row1['category']}.{$row1['s_id']}.{$row2['s_id']}.$freq.$tr.$corrtype";
              
              if (in_array($s_corr_nid,$existingUniqIdentifiers) ) {
                $uTagsCorr[] = array('updated' => (bool) FALSE,
                                                  'errorMsg' => 'Already exists',
                                                  's_corr_nid' => $s_corr_nid,
                                                  'fk_id_1' => $row1['s_id'],
                                                  'fk_id_2' => $row2['s_id'],
                                                  'freq' => $freq,
                                                  'trail' => $tr,
                                                  'corr_type' => $corrtype
                                                  );
                continue;
              }
              
              else {
                $uTagsCorr[] = array('updated' => (bool) TRUE,
                                                  's_corr_nid' => $s_corr_nid,
                                                  'fk_id_1' => $row1['s_id'],
                                                  'fk_id_2' => $row2['s_id'],
                                                  'freq' => $freq,
                                                  'trail' => $tr,
                                                  'corr_type' => $corrtype
                                    );


                $sqldata[] = array(
                                  's_corr_nid' => $s_corr_nid,
                                  'category' => $row1['category'],
                                  'fk_id_1' => $row1['s_id'],
                                  'fk_id_2' => $row2['s_id'],
                                  'freq' => $freq,
                                  'trail' => $tr,
                                  'corr_type' => $corrtype,
                                  'last_updated' => date('Y-m-d H:i:s')
                                );
                //print_r($row1);print_r($row2);
              }
              
              
            }
            }
        }
        
        //print_r($sqldata);
        //(new TestOutput($sqldata) )-> print();
    
}

//exit();
if ( count($sqldata) > 0) {
  $colnames = array('s_corr_nid','category','fk_id_1','fk_id_2','freq','trail','corr_type','last_updated');
  $sql -> multipleInsert('tags_correl',$colnames,$sqldata);
}
