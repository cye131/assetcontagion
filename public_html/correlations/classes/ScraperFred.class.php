<?php
class ScraperFred {
    
    public function __construct ($tickersAndInfo) {
        $this->apiKey = (string) '46852003f0fcabfb873c1497954286ed';
        $this->tickersAndInfo = (array) $tickersAndInfo;
    }
    
    public function return_results () {
        $results = array();
        foreach ($this->tickersAndInfo as $tickerInfo) {
            $fredData = $this->get_fred_data($tickerInfo['lookup_code'],$tickerInfo['last_updated'],$tickerInfo['id']);
            $results = array_merge_recursive($results,$fredData);
        }
        return $results;
    }
    
    
    public function get_fred_data ($lookupCode,$minDate,$fkId) {
        $ch = curl_init();
    
        $url = "https://api.stlouisfed.org/fred/series/observations?series_id=$lookupCode&units=lin&frequency=d&sort_order=asc&observation_start=$minDate&observation_end=9999-12-31&api_key={$this->apiKey}&file_type=json";
        echo $url;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    
        $arr = json_decode(curl_exec($ch),true);
        
        curl_close($ch);
        
        $historicaldata = array();
        foreach ($arr['observations'] as $k => $row) {
            if (!isset($row['value']) || $row['value'] == 0) continue;
    
            $date = (string) $row['date'];
            $id = str_replace('-','',$date).'.'.$fkId;

            //$historicaldata[$date]['id'] = str_replace('-','',$date).'.'.$this->tags_fred_id;            
            $historicaldata[$id]['date'] = $date;
            $historicaldata[$id]['value'] = (float) $row['value'];

            if ($k > 0) $historicaldata[$id]['chg'] = ($row['value']-$lastvalue)/$lastvalue;
            else $historicaldata[$id]['chg'] = NULL;
                
            //$historicaldata[$date]['tags_fred_id'] = $this->tags_fred_id;
            
            $lastvalue = $row['value'];
        }
        
        return $historicaldata;
    
    }

}