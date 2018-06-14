<?php
class ScraperFid {
        
    public function __construct($tickersAndInfo) {
        $this->tickersAndInfo = (array) $tickersAndInfo;
        $this->maxCurlAmount = (int) 5;
    }
    
    
    public function return_results () {
        //Seperate by every 5 rows
        $i = (int) 0; $j = (int) 0;
        $groupsOfFive = array();
        
        foreach ($this->tickersAndInfo as $tickerInfo) {
            if ( ($i%($this->maxCurlAmount)) === 0 && $i!== 0) {//every 5th row, create a new $urlstr subarray
                $j++;
            }
            $groupsOfFive[$j][] = array('lookup_code' => $tickerInfo['lookup_code'],
                                                        'id' => $tickerInfo['id'],
                                                        'last_updated' => !is_null($tickerInfo['last_updated']) ? strtotime($tickerInfo['last_updated']) : strtotime('1980-01-01')
                                                        );
            $i++;
        }
        //(new TestOutput($groupsOfFive)) -> print();

        //Pulls data via curl
        $results = array();
        foreach ($groupsOfFive as $fiveSecurities) {
            $minDate = date('Y/m/d', min(array_column($fiveSecurities,'last_updated')) );
            $html = $this->fetchData(array_column($fiveSecurities,'lookup_code'),$minDate,NULL);
            $json = $this->cleanAndDecodeData($html);
            $fidData = $this->createTSArrayFromJsonArray($json);
            
            $results = array_merge_recursive($results,$fidData);
        }
        
        return $results;
    }
    
    
    
    /* Pulls data from Fidelity website
     *
     *
     *
     */
    public function fetchData($tickers,$mindate = NULL,$maxdate = NULL) {
        //print_r( $tickers );
        
        if (is_null($mindate)) $mindate = date('Y/m/d',strtotime('-1 month'));
        if (is_null($maxdate)) $maxdate = date('Y/m/d');
        
        if (is_array($tickers) === TRUE) {
            $tickerstring = implode($tickers,",");
        }
        elseif (is_string($tickers) === TRUE) {
            $tickerstring = $tickers;
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
        echo $url;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        $html = curl_exec($ch);
        curl_close($ch);
        
        return $html;
    }
    
    
    
    /* Parses data into array
     *
     *
     *
     */
    public function cleanAndDecodeData($html) {
        
        if (strlen($html) == 0) {
            echo 'No HTML entered.';
            exit();
        } else {
            $clean = substr($html,0,-2); //remove opening and closing ( in data
            $clean = substr($clean,2);
            $clean = json_decode($clean,true);
            $json = $clean['SYMBOL'];
            
            //print_r ($json);
            return $json;
        }

    }

    /* Converts the array into an associative array indexed by date
     *
     *
     *
     */
    public function createTSArrayFromJsonArray($json) {
        
        $tsarray = array();
        foreach ($json as $row) {
            if (!isset($row['IDENTIFIER']) || !isset($row['BARS']['CB'])) {
                echo 'Error: Missing Identifier or Historical Data';
                continue;
            }
            
            $identifier = $row['IDENTIFIER'];
            $fk_tags_id = $this->tickersAndInfo[array_search($identifier,$this->tickersAndInfo)]['id'];
            
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
        
        return $tsarray;
        
    }
    
    
    /* Returns first date of securities
     *
     *
     *
     */
    /*
    public function getFirstDate() {
        if ($this->tickercount === 1) {
            $this->firstdate = $this->fid_date($this->json[0]['BARS']['CB'][0]['lt']);
        } else {
            echo 'Function not yet implemented for multiple securities';
            return;
        }
        
        //print_r ($this->firstdate);
        return $this->firstdate;

    }
    */
    
    private function fid_date($str) {
        $timestamp = new DateTime();
        $timestamp = DateTime::createFromFormat('!m-d-Y::H:i:s',$str)->getTimestamp();
        return (string) date('Y-m-d',$timestamp);
    }


}