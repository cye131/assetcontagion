<?php
$varsToBind = [];

$category = $fromAjax['category'] ?? $fromRouter['category'] ?? NULL;
$corr_type = $fromAjax['corr_type'] ?? $fromRouter['corr_type'] ?? NULL;
$freq = $fromAjax['freq'] ?? $fromRouter['freq'] ?? NULL;
$trail = $fromAjax['trail'] ?? $fromRouter['trail'] ?? NULL;




if (!is_null($category)) {
    $category_str = 'AND cat_nid LIKE CONCAT(:category,"%")';
    $varsToBind['category'] = $category;
} else $category_str = '';

if (!is_null($corr_type)) {
    $corr_type_str = 'AND corr_type = :corr_type';
    $varsToBind['corr_type'] = $corr_type;
} else $corr_type_str = '';

if (!is_null($freq)) {
    $freq_str = 'AND freq = :freq';
    $varsToBind['freq'] = $freq;
} else $freq_str = '';

if (!is_null($trail)) {
    $trail_str = 'AND trail = :trail';
    $varsToBind['trail'] = $trail;
} else $trail_str = '';



$histCorrIndex = $sql->selectToAssoc("
SELECT
*
FROM hist_corrindex

WHERE (1 = 1
$category_str
$corr_type_str
$freq_str
$trail_str
)

",$varsToBind,'');
