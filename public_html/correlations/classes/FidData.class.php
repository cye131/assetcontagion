<?php
class FidData {
        
    public function __construct($dev_mode = TRUE) {
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
    public function fetchData($tickers,string $mindate = NULL,string $maxdate = NULL) {
        echo $this->dev_mode;
                
        if (is_null($mindate)) $mindate = date('Y/m/d',strtotime('-1 week'));
        if (is_null($maxdate)) $maxdate = date('Y/m/d');
        
        if (is_array($tickers) === TRUE) {
            $tickerstring = implode($tickers,",");
            $this->tickercount = count($tickers);
        }
        elseif (is_string($tickers) === TRUE) {
            $tickerstring = $tickers;
            $this->tickercount = 1;
        }
        else {
            echo 'Error: Tickerstring not string or array';
            exit();
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
            
            foreach ($row['BARS']['CB'] as $k => $point) {
                $date = (string) fid_date($point['lt']);                
                $tsarray[$identifier][$date] = array(
                                                     'close' => (float) $point['cl'],
                                                     );
                
                if ($k > 0) $tsarray[$identifier][$date]['roi'] = (float) round(($point['cl']-$lastprice)/$lastprice,4);
                else $tsarray[$identifier][$date]['roi'] = (float) 0;
                $lastprice = (float) $point['cl']; 
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
            $this->firstdate = fid_date($this->json[0]['BARS']['CB'][0]['lt']);
        } else {
            if ($this->dev_mode === TRUE) echo 'Function not yet implemented for multiple securities';
            return;
        }
        
        if ($this->dev_mode === TRUE) print_r ($this->firstdate);
        return $this->firstdate;

    }

    
}

?>