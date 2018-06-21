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

WHERE freq IS NOT NULL
$category_str

ORDER BY base.category,base.b_id,series.freq
",$varsToBind,'');