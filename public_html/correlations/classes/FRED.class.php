<?php

class FRED {
    
    public function __construct ($seriesname,$freq,$tags_fred_id) {
        $this->api_key = (string) '46852003f0fcabfb873c1497954286ed';
        $this->seriesname = (string) $seriesname;
        $this->freq = (string) $frequency;
        $this->historicaldata = array();
        $this->sql = (string) '';
        $this->tags_fred_id = (string) $tags_fred_id;
    }
    
    public function get_fred_data () {
        $ch = curl_init();
    
        $url = 'https://api.stlouisfed.org/fred/series/observations?series_id='.$this->seriesname.'&units=lin&frequency='.$this->freq.'&sort_order=asc&observation_start=1776-07-04&observation_end=9999-12-31&api_key='.$this->api_key.'&file_type=json';
        
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

            $historicaldata[$date]['id'] = str_replace('-','',$date).'.'.$this->tags_fred_id;            
            $historicaldata[$date]['date'] = $date;
            $historicaldata[$date]['value'] = (float) $row['value'];

            if ($k > 0) $historicaldata[$date]['chg'] = ($row['value']-$lastvalue)/$lastvalue;
            else $historicaldata[$date]['chg'] = NULL;
                
            $historicaldata[$date]['tags_fred_id'] = $this->tags_fred_id;
            
            $lastvalue = $row['value'];
        }
        
        $this->historicaldata = $historicaldata;
        unset($historicaldata);
        return $this->historicaldata;
    
    }

}