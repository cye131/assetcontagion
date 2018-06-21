<?php
$date = $fromAjax['date'] ?? NULL;
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
