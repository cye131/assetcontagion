<?php
$date = $fromAjax['date'] ?? $fromRouter['date'] ?? NULL;
$category = $fromAjax['category'] ?? $fromRouter['category'] ?? NULL;
$corr_type = $fromAjax['corr_type'] ?? $fromRouter['corr_type'] ?? NULL;
$freq = $fromAjax['freq'] ?? $fromRouter['freq'] ?? NULL;
$trail = $fromAjax['trail'] ?? $fromRouter['trail'] ?? NULL;



if (!is_null($category)) {
    $category_str = 'AND t1b.category LIKE CONCAT(:category,"%") AND t2b.category LIKE CONCAT(:category,"%") ';
    $varsToBind['category'] = $category;
} else $category_str = '';

if (!is_null($corr_type)) {
    $corr_type_str = 'AND t0.corr_type = :corr_type';
    $varsToBind['corr_type'] = $corr_type;
} else $corr_type_str = '';

if (!is_null($freq)) {
    $freq_str = 'AND t0.freq = :freq';
    $varsToBind['freq'] = $freq;
} else $freq_str = '';

if (!is_null($trail)) {
    $trail_str = 'AND t0.trail = :trail';
    $varsToBind['trail'] = $trail;
} else $trail_str = '';









$dayOfWeek = date('w',strtotime($date)) + 1;






$dateCount = $sql->selectToAssoc("
SELECT pretty_date,COUNT(fk_id) AS num_rows FROM `hist_correl` 
WHERE (
	(pretty_date >= (SELECT MIN(obs_start) FROM `tags_correl` WHERE (category='reg' AND freq='d' AND trail = 30) ))
	AND
	(pretty_date <= (SELECT MAX(obs_end) FROM `tags_correl` WHERE (category='reg' AND freq='d' AND trail = 30) ))
)
GROUP BY pretty_date

HAVING num_rows >= 500 AND DAYOFWEEK(pretty_date) = $dayOfWeek
ORDER BY pretty_date ASC

",'','');
