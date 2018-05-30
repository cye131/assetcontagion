<?php

class FRED {
    
    public function __construct ($seriesname) {
        $this->api_key = '46852003f0fcabfb873c1497954286ed';
        $this->seriesname = $seriesname;
        $this->historicaldata = array();
        $this->sql = '';
    }
    
    public function get_fred_data () {
        $ch = curl_init();
    
        $url = 'https://api.stlouisfed.org/fred/series/observations?series_id='.$this->seriesname.'&units=lin&sort_order=asc&observation_start=1776-07-04&observation_end=9999-12-31&api_key='.$this->api_key.'&file_type=json';
        
        echo $url;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    
        $arr = json_decode(curl_exec($ch),true);
        
        curl_close($ch);
        
        foreach ($arr['observations'] as $k => $row) {
            if (!isset($row['value']) || $row['value'] == 0) continue;
    
            $date = (string) $row['date'];

            $historicaldata[$date]['id'] = $this->seriesname.'.'.$date;            
            $historicaldata[$date]['code'] = $this->seriesname;
            $historicaldata[$date]['date'] = $date;
            $historicaldata[$date]['value'] = (float) $row['value'];

            if ($k > 0) $historicaldata[$date]['chg'] = ($row['value']-$lastvalue)/$lastvalue;
            else $historicaldata[$date]['chg'] = NULL;
            
            $lastvalue = $row['value'];
        }
        
        $this->historicaldata = $historicaldata;
        unset($historicaldata);
        return $this->historicaldata;
    
    }

}

?>