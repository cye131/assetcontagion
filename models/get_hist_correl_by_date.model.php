<?php
$varsToBind = [];

$date = $fromAjax['date'] ?? $fromRouter['date'] ?? date('Y-m-d') ?? '';
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

$varsToBind['pretty_date'] = $date;

$tagsCorrel = $sql->selectToAssoc("
SELECT
t0.*,

t1.s_id AS s_id_1,t1.freq AS freq_1,
t2.s_id AS s_id_2,t2.freq AS freq_2,

t1b.b_id AS b_id_1, t1b.name AS name_1,t1b.grouping AS grouping_1,t1b.lookup_code AS lookup_code_1,t1b.proxy AS proxy_1,
t2b.b_id AS b_id_2, t2b.name AS name_2,t2b.grouping AS grouping_2,t2b.lookup_code AS lookup_code_2,t2b.proxy AS proxy_2,

th.value as h_value, th.pretty_date as h_pretty_date

FROM tags_correl t0

LEFT JOIN tags_series AS t1
ON t0.fk_id_1 = t1.s_id
LEFT JOIN tags_series_base AS t1b
ON t1.fk_id = t1b.b_id

LEFT JOIN tags_series AS t2
ON t0.fk_id_2 = t2.s_id
LEFT JOIN tags_series_base AS t2b
ON t2.fk_id = t2b.b_id

LEFT JOIN hist_correl AS th
ON (t0.s_corr_id = th.fk_id)

WHERE (th.pretty_date = :pretty_date
$category_str
$corr_type_str
$freq_str
$trail_str
)
",$varsToBind,'');
