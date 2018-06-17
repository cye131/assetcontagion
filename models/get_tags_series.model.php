<?php

$varsToBind = [];

$category = $fromAjax['category'] ?? $fromRouter['category'] ?? NULL;

if (!is_null($category)) {
    $category_str = 'AND category LIKE CONCAT(:category,"%")';
    $varsToBind['category'] = $category;
} else {
    $category_str = '';
}


$tagsSeries = $sql -> selectToAssoc("
SELECT *
FROM `tags_series` AS series
LEFT JOIN `tags_series_base` AS base
ON series.fk_id = base.b_id

WHERE freq='d'
$category_str

ORDER BY base.category,base.b_id,series.freq
",$varsToBind,'');

/*
$lookupArray = array(); $categories = array();
foreach ($tagsSeries as $tag) {
    $lookupArray[$tag['source']][] = array( 'lookup_code' => $tag['lookup_code'],
                                                                    'id' => $tag['id'],
                                                                   'last_updated' => $tag['last_updated'] ?? NULL
                                          );
}*/
/*
$categories =  array_column($tagsSeries,'category');
$categories = json_encode(array_values(array_unique($categories)));
$json = json_encode($tagsSeries);

$modeldata['script']  .= "tagsSeries=$json;categories=$categories";
*/

/* Passes an array indexed by source type with sub-elements for lookup_code and last_updated
 *
 */
/*$i = (int) 0;
foreach ($lookupArray as $source => $tickersAndInfo) {
    $className = 'Scraper'.ucfirst($source);
    $scraper[$i] = new $className($tickersAndInfo);
    $historicalData[$i] = $scraper[$i] -> return_results();
    
    $i++;
}


(new TestOutput($historicalData)) -> print();
*/