<?php

class CorrelationData {
    
    public function __construct($series1,$series2,$seriesnames) {
        $this->series1 = $series1;
        $this->series2 = $series2;
        $this->seriesnames = $seriesnames;
        $this->correlationdata = array();
        $this->correlationindex = array();
    }
    
    
    public static function getDupletCombinations($array) {
        //takes array [a,b,c] returns [[ab],[ac],[bc]]
        $combinations = array();
        sort($array);
        
        $k = (int) 0;
        
        for ($i=0;$i<count($array);$i++) {
            for ($j=$i+1;$j<count($array);$j++) {
                $combinations[$k][0] = $array[$i];
                $combinations[$k][1] = $array[$j];
            
            $k++;
            }
            
        }

        return $combinations;        
    }
    
    public static function pearsonCorrelation($x, $y){
        $length= count($x);
        $mean1=array_sum($x) / $length;
        $mean2=array_sum($y) / $length;
        $a=0;
        $b=0;
        $axb=0;
        $a2=0;
        $b2=0;
        for ($i=0;$i<$length;$i++) {
            $a=$x[$i]-$mean1;
            $b=$y[$i]-$mean2;
            $axb=$axb+($a*$b);
            $a2=$a2+ pow($a,2);
            $b2=$b2+ pow($b,2);
        }
        if ( sqrt($a2*$b2) == 0 ) $corr = NULL;
        else $corr= $axb / sqrt($a2*$b2);
        return $corr;
    }

    
    
    public function calculateCorrelation($colNames,$freqCount) {
    //Takes data of the form array->date->roi/value/etc
        ksort($this->series1);
        ksort($this->series2);
        
        $correlation = array('index'=>array(),
                             'data'=>array()
                             );
        
        $combinedseries = array($this->seriesnames[0] => $this->series1, $this->seriesnames[1] => $this->series2);
        
        $this->calculateCorrelationData($combinedseries,$colNames,$freqCount);
        $this->calculateCorrelationIndex($combinedseries,$colNames,$freqCount);

    }
    
    
    private function calculateCorrelationData ($combinedseries,$colNames,$freqCount) {
        //creates the historical data sub-arrays
        //$data['json_0'] = array();
        //$data['json_1'] = array();
        //$data['json_correlation'] = array();
    
        $data['timeseries'] = array(); //this will index both data1 and data2 under the same date to allow us to check if both data points exist on the same days
    
        foreach ($combinedseries as $code => $tsdata) {
            if (!in_array($code,$this->seriesnames)) continue;
            
            if ($code === $this->seriesnames[0]) $level = (int) 0;
            elseif ($code === $this->seriesnames[1]) $level = (int) 1;
            $colUsed = $colNames[$level];
            
            //$datalevel_l30 = (string) $datalevel.'_l30';
                
            $i = (int) 0;
            //puts the data into the sub-arrays
            foreach ($tsdata as $tsdate => $tsrow) {
                
                //$data['json_'.$level][$i][0] = (integer) strtotime($tsdate) * 1000; //Needs to be *1000 for Javascript to read
                //$data['json_'.$level][$i][1] = (float) number_format($tsrow[$colUsed],6);
                
                
                $data['timeseries'][$tsdate]['date'] = (string) $tsdate;
                if (isset($tsrow[$colUsed]) && !is_null($tsrow[$colUsed])) {
                    $data['timeseries'][$tsdate][$level] = (float) number_format($tsrow[$colUsed],6);
                } else {
                    $data['timeseries'][$tsdate][$level] = NULL;
                }
    
                //$data['timeseries'][$tsdate][$datalevel.'_'.{$colUsed}] = (float) $tsrow['roi'];
                            
                $i++;
            }
            
        }
        
        ksort($data['timeseries']);
        
        
        //calculates correlation checking that both data points exist using the timeseries subarray
        $data1_l = array();
        $data2_l = array();
        $dates_l = array();
        foreach ($data['timeseries'] as $date => $tsrow) {
            if (!isset($tsrow[0]) || !isset($tsrow[1]) || is_null($tsrow[0]) || is_null($tsrow[1]) ) continue;

            $data1_l[] = $tsrow[0];
            $data2_l[] = $tsrow[1];
            $dates_l[] = $date;
            
            if  (count($data1_l) > $freqCount) {
                array_shift($data1_l);
                array_shift($data2_l);
                array_shift($dates_l);
            }
            
            if  (count($data1_l) === $freqCount) {
                $corr = $this::pearsonCorrelation($data1_l,$data2_l);
                if (!is_null($corr)) $data['timeseries'][$date]['correlation'] = (float) round($corr,4);
                else $data['timeseries'][$date]['correlation'] = NULL;
                
                $data['timeseries'][$date]['inputs_used'] = count($dates_l);
                $data['timeseries'][$date]['earliest_input'] = $dates_l[0];
            }
            
        }
        return $this->correlationdata = $data;

    }
    
    private function calculateCorrelationIndex($combinedseries,$colNames,$freqCount) {
        $index = array();
        $index['codes'] = array($this->seriesnames[0],$this->seriesnames[1]);
        $index['valuestocorrelate'] = $colNames;

        //calculates first (non-necessarily shared) date for each data sets
        for ($i = 0; $i <= 1; $i++) $index['firstdatadates'][$i] = array_keys($combinedseries[$this->seriesnames[$i]])[0];
        
        //calculates first shared date
        foreach ($this->series1 as $date=>$row) $dates1[] = $date;
        foreach ($this->series2 as $date=>$row) {
            if (in_array($date,$dates1)) {$index['firstshareddatadate'] = $date;break;}
        }
        
        //calculates most recent correlation and date of; also calculates # of data points w/correlation non-null
        $countcorrelation = (int) 0;
        $reverseddatearray = $this->correlationdata['timeseries'];
        krsort($reverseddatearray);
        foreach ($reverseddatearray as $date=>$row) {
            if ( isset($row['correlation']) && !is_null($row['correlation']) ) {
                $countcorrelation ++;
                if ($countcorrelation === 1) {
                    $index['correl_mostrecent_date'] = $date;
                    $index['correl_mostrecent_val'] = $row['correlation'];
                    $index['correl_mostrecent_earliestinput_date'] = $date;
                } elseif ($countcorrelation === $freqCount) {
                    $index['correl_freqago_date'] = $date;
                }
            }
        }
        
        $index['countcorrelationdata'] = $countcorrelation;
        if ($countcorrelation >= 1) {
            $index['UPDATED'] = (bool) TRUE;
        } else {
            $index['UPDATED'] = (bool) FALSE;
        }
        
        
        //returns "sources of data"
        for ($i = 0; $i <= 1; $i++) {
            //$index['sources'][$i] = $combinedseries[$this->seriesnames[$i]][$index['firstdatadates'][$i]]['source'];
            //$index['fk_ids'][$i] = $combinedseries[$this->seriesnames[$i]][$index['firstdatadates'][$i]]['fk_tags_id'];
        }
        
        return $this->correlationindex = $index;
    
    }

    
    
    
    
    
    
    
    
    
    
    
    
}


?>