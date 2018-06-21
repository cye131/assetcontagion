<?php
class DataScraperFred {
    
    public function __construct ($series) {
        $this->api_key = (string) '46852003f0fcabfb873c1497954286ed';
        $this->fk_id = $series['s_id'];
        $this->lookup_code = $series['lookup_code'];
        $this->url = '';
            
        $freqArray = ['d' => 'd' ,'w' => 'wem', 'm' => 'm', 'q' => 'q' ,'a' => 'a'];
        $this->freq = $freqArray[$series['freq']];
        $this->min_date = isset( $series['obs_end']) && is_string($series['obs_end']) && strlen($series['obs_end']) > 0 ? $series['obs_end'] : '1930-01-01';
    }
    
    public function return_results () {
        $results = array();
        $data = $this->get_fred_data();
        
        $results['data'] = $data;
        $results['url'] = $this->url;
        
        return $results;
    }
    
    
    private function get_fred_data () {
        $ch = curl_init();
    
        $this->url = "https://api.stlouisfed.org/fred/series/observations?series_id={$this->lookup_code}&units=lin&frequency={$this->freq}&sort_order=asc&observation_start={$this->min_date}&observation_end=9999-12-31&api_key={$this->api_key}&file_type=json";
        //echo $url;
        
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    
        $arr = json_decode(curl_exec($ch),true);
        
        curl_close($ch);
        
        $tsarray = array();
        $i = (int) 0;
        foreach ($arr['observations'] as $row) {
            if (!isset($row['value']) || $row['value'] == 0 || $row['value'] == '.') continue;
    
            $date = (string) $row['date'];
            //$id = str_replace('-','',$date).'.'.$this->fk_id;

            $tsarray[] = array(//'h_id' => $id,
                                    'date' => $date,
                                    'pretty_date' => $this->date_cleaner( $date ),
                                    'value' => (float) $row['value'],
                                    'chg' => ($i>0) ? (float) ($row['value']-$lastvalue)/$lastvalue : (float)  0,
                                    'fk_id' => $this->fk_id
                                );

            $lastvalue = (float) $row['value'];
            
            $i++;
        }
        
        return $tsarray;
    
    }
    
    private function date_cleaner($date) {
        if ($this->freq === 'd') return $date;
        elseif ($this->freq === 'wem') return $date;
        elseif ($this->freq === 'm') {
            return date('Y-m-d', strtotime('+1 month',strtotime($date)));
        }
        elseif ($this->freq === 'q')  {
            return date('Y-m-d', strtotime('+3 months',strtotime($date)));
        }
        elseif ($this->freq === 'a') {
            return date('Y-m-d', strtotime('+1 year',strtotime($date)));
        }
    }


}