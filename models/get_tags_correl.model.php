<?php
$varsToBind = [];

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





$tagsCorrel = $sql->selectToAssoc("
SELECT
t0.s_corr_id,t0.s_corr_nid,t0.category,t0.fk_id_1,t0.fk_id_2,t0.freq,t0.trail,t0.corr_type,t0.obs_start,t0.obs_end,t0.obs_end_val,t0.obs_end_input_min,t0.obs_count,t0.last_updated,

t1.s_id AS s_id_1,t1.freq AS freq_1,
t2.s_id AS s_id_2,t2.freq AS freq_2,

t1b.b_id AS b_id_1, t1b.name AS name_1,t1b.grouping AS grouping_1,t1b.lookup_code AS lookup_code_1,t1b.proxy AS proxy_1,t1b.code AS code_1,
t2b.b_id AS b_id_2, t2b.name AS name_2,t2b.grouping AS grouping_2,t2b.lookup_code AS lookup_code_2,t2b.proxy AS proxy_2,t2b.code AS code_2

FROM tags_correl t0

LEFT JOIN tags_series AS t1
ON t0.fk_id_1 = t1.s_id
LEFT JOIN tags_series_base AS t1b
ON t1.fk_id = t1b.b_id

LEFT JOIN tags_series AS t2
ON t0.fk_id_2 = t2.s_id
LEFT JOIN tags_series_base AS t2b
ON t2.fk_id = t2b.b_id

WHERE (1 = 1
$category_str
$corr_type_str
$freq_str
$trail_str
)

",$varsToBind,'');

