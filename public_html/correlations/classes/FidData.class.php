<?php
class FidData {
        
    public function __construct($data,$dev_mode = TRUE) {
        $this->tickers = array_column($data,'lookup_code');
        $this->data = $data;
        $this->dev_mode = (boolean) $dev_mode;
        $this->html = '';
        $this->tsarray = array();
        $this->tickercount = (int) 1;
    }
    
    /* Pulls data from Fidelity website
     *
     *
     *
     */
    public function fetchData(string $mindate = NULL,string $maxdate = NULL) {
        echo $this->dev_mode;
        print_r( $this->tickers );
        
        if (is_null($mindate)) $mindate = date('Y/m/d',strtotime('-1 month'));
        if (is_null($maxdate)) $maxdate = date('Y/m/d');
        
        if (is_array($this->tickers) === TRUE) {
            $tickerstring = implode($this->tickers,",");
            $this->tickercount = count($this->tickers);
        }
        elseif (is_string($this->tickers) === TRUE) {
            $tickerstring = $this->tickers;
            $this->tickercount = 1;
        }
        else {
            echo 'Error: Tickerstring not string or array';
            return;
        }

        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 0);
        
        $headers = array(
            'Host: fastquote.fidelity.com',
            'Referer: https://eresearch.fidelity.com/eresearch/markets_sectors/sectors/sectors_in_market.jhtml',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $url = 'https://fastquote.fidelity.com/service/historical/chart/lite/json?productid=research&symbols='.$tickerstring.'&dateMin='.$mindate.':00:00:00&dateMax='.$maxdate.':00:00:00&intraday=n&granularity=1&incextendedhours=n&dd=y';
        
        if ($this->dev_mode === TRUE) echo $url;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        $html = curl_exec($ch);
        curl_close($ch);
        
        return $this->html = $html;
    }
    
    /* Parses data into array
     *
     *
     *
     */
    public function cleanAndDecodeData() {
        
        if (strlen($this->html) == 0) {
            if ($this->dev_mode === TRUE) echo 'No HTML entered.';
            exit();
        } else {
            $clean = substr($this->html,0,-2); //remove opening and closing ( in data
            $clean = substr($clean,2);
            $clean = json_decode($clean,true);
            $this->json = $clean['SYMBOL'];
            
            if ($this->dev_mode === TRUE) print_r ($this->json);
            return $this->json;
        }

    }

    /* Converts the array into an associative array indexed by date
     *
     *
     *
     */
    public function createTSArrayFromJsonArray() {
        
        $tsarray = array();
        foreach ($this->json as $row) {
            if (!isset($row['IDENTIFIER']) || !isset($row['BARS']['CB'])) {
                if ($this->dev_mode === TRUE) echo 'Error: Missing Identifier or Historical Data';
                continue;
            }
            
            $identifier = $row['IDENTIFIER'];
            $fk_tags_id = $this->data[array_search($identifier,$this->tickers)]['id'];
            
            foreach ($row['BARS']['CB'] as $k => $point) {
                $date = (string) $this->fid_date($point['lt']);
                $id = str_replace('-','',$date).'.'.$fk_tags_id;
                $tsarray[$id] = array('id' => $id,
                                        'date' => str_replace('-','',$date),
                                        'value' => (float) $point['cl'],
                                        'chg' => null,
                                        'fk_tags_id' => null
                                    );
                
                if ($k > 0) $tsarray[$id]['chg'] = (float) round(($point['cl']-$lastprice)/$lastprice,4);
                else $tsarray[$id]['chg'] = (float) 0;
                $lastprice = (float) $point['cl'];
                
                $tsarray[$id]['fk_tags_id'] = $fk_tags_id;
            }
        }
        
        $this-> tsarray = $tsarray;

        if ($this->dev_mode === TRUE) print_r ($this->tsarray);
        return $this-> tsarray;
        
    }
    
    
    /* Returns first date of securities
     *
     *
     *
     */
    public function getFirstDate() {
        if ($this->tickercount === 1) {
            $this->firstdate = $this->fid_date($this->json[0]['BARS']['CB'][0]['lt']);
        } else {
            if ($this->dev_mode === TRUE) echo 'Function not yet implemented for multiple securities';
            return;
        }
        
        if ($this->dev_mode === TRUE) print_r ($this->firstdate);
        return $this->firstdate;

    }
    
    
    private function fid_date($str) {
        $timestamp = new DateTime();
        $timestamp = DateTime::createFromFormat('!m-d-Y::H:i:s',$str)->getTimestamp();
        return (string) date('Y-m-d',$timestamp);
    }


    
}

?>