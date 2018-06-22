<?php

class CorrelationData {
    
    public function __construct($combinedSeries,$corrTag) {
        //$combinedSeries takes 2 series together in a multidimensional array
        //$corrTag is a one-dimensional keyed array with 'freq','trail','val_type_1','val_type_2' , corr_type mandatory
        $this->combinedSeries = $combinedSeries;
        $this->seriesNames = array_keys($combinedSeries);
        $this->freq = $corrTag['freq'] ?? NULL;
        $this->trail = $corrTag['trail'] ?? NULL;
        $this->val_type_1 = 'chg';
        $this->val_type_2 = 'chg';
        $this->corr_type = $corrTag['corr_type'] ?? NULL;

        foreach ($corrTag as $k=>$v) {
            $this->$k = $v;
        }
        
        $this->correlData = array();
        $this->correlIndex = array();
    }
    
    
    

    
    public function calculateCorrelation() {
    //Takes data of the form array->date->roi/value/etc        
        $this->calculateCorrelationData();
        $this->calculateCorrelationIndex();

        return ['data' => $this->correlData,'index' => $this->correlIndex];
    }
    
    
    private function calculateCorrelationData () {
        $data = ['timeseries' =>array()]; //this will index both data1 and data2 under the same date to allow us to check if both data points exist on the same days
    
        foreach ($this->combinedSeries as $code => $seriesData) {
            if (!in_array($code,$this->seriesNames)) continue;
            
            if ($code === $this->seriesNames[0]) $level = (int) 1;
            elseif ($code === $this->seriesNames[1]) $level = (int) 2;
            else {echo $code;print_r( $this->seriesNames );exit();}
            
            //echo $level;
            //echo $this->freq;echo $this->trail;
            $colUsed = $this->{'val_type_'.$level};
            
                
            $i = (int) 0;
            //puts the data into the sub-arrays
            foreach ($seriesData as $tsDate => $tsRow) {
                //$data['json_'.$level][$i][0] = (integer) strtotime($tsdate) * 1000; //Needs to be *1000 for Javascript to read
                //$data['json_'.$level][$i][1] = (float) number_format($tsrow[$colUsed],6);
                
                $data['timeseries'][$tsDate]['date'] = (string) $tsDate;
                if (isset($tsRow[$colUsed]) && !is_null($tsRow[$colUsed])) {
                    $data['timeseries'][$tsDate][$level] = (float) number_format($tsRow[$colUsed],6);
                } else {
                    $data['timeseries'][$tsDate][$level] = NULL;
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
        foreach ($data['timeseries'] as $date => $tsRow) {
            if (!isset($tsRow[1]) || !isset($tsRow[2]) || is_null($tsRow[1]) || is_null($tsRow[2]) ) {
                continue;
            }
            
            $data1_l[] = $tsRow[1];
            $data2_l[] = $tsRow[2];
            $dates_l[] = $date;

            if  (count($data1_l) > $this->trail) {
                array_shift($data1_l);
                array_shift($data2_l);
                array_shift($dates_l);
                //$data['timeseries'][$date]['array_shifted'] = (int) 1;
                //$data['timeseries'][$date]['data_count'] = count($data1_l);
            }
            
            if  (count($data1_l) === (int) $this->trail) {

                $corr = $this->getCorr($data1_l,$data2_l,$this->corr_type);
                                                                                                      
                $data['timeseries'][$date]['correlation'] = !is_null($corr) ? (float) round($corr,4) : NULL;
                $data['timeseries'][$date]['inputs_used'] = count($dates_l);
                $data['timeseries'][$date]['earliest_input'] = $dates_l[0];
            }
            
        }
        return $this->correlData = $data;

    }
    
    private function calculateCorrelationIndex() {
        $index = array();
        $index['codes'] = $this->seriesNames;
        $index['valuestocorrelate'] = [$this->val_type_1,$this->val_type_2];
        
        //calculates first shared date
        foreach ($this->combinedSeries[$this->seriesNames[0]] as $date=>$row) $dates1[] = $date;
        foreach ($this->combinedSeries[$this->seriesNames[1]] as $date=>$row) {
            if (in_array($date,$dates1)) {$index['firstshareddatadate'] = $date;break;}
        }
        
        //calculates most recent correlation and date of; also calculates # of data points w/correlation non-null
        $countcorrelation = (int) 0;
        $reverseddatearray = $this->correlData['timeseries'];
        krsort($reverseddatearray);
        foreach ($reverseddatearray as $prettyDate=>$row) {
            if ( isset($row['correlation']) && !is_null($row['correlation']) ) {
                $countcorrelation ++;
                if ($countcorrelation === 1) {
                    $index['correl_last_date'] = $prettyDate;
                    $index['correl_last_val'] = $row['correlation'];
                    $index['correl_last_earliestinput_date'] = $row['earliest_input'];
                } elseif ($countcorrelation === $this->trail) {
                    $index['correl_trail_date'] = $prettyDate;
                }
                
                $firstCorrelDate = $prettyDate;
            }
        }
        
        $index['correl_first_date'] = $firstCorrelDate ?? NULL;

        
        
        $index['correl_count'] = $countcorrelation;
        if ($countcorrelation >= 1) {
            $index['UPDATED'] = (bool) TRUE;
        } else {
            $index['UPDATED'] = (bool) FALSE;
        }
        
        return $this->correlIndex = $index;
    
    }

    
    
    
    
    
    public static function getDupletCombinations($array) {
        //takes array [a,b,c] returns [[ab],[ac],[bc]]
        $combinations = array();
        
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
    
    public function getCorr ($x,$y,$type) {
        if ($type === 'rho') return $this->pearsonCorrelation($x,$y,$type);
        elseif ($type === 'ktau') return $this->kendallsTau($x,$y,$type);
        elseif ($type === 'srho') return $this->spearmansRho($x,$y,$type);
    }
    
    
    private function pearsonCorrelation($x, $y){
        $length = count($x);
        $mean1 = array_sum($x) / $length;
        $mean2 = array_sum($y) / $length;
        $a = 0;
        $b = 0;
        $axb = 0;
        $a2 = 0;
        $b2 = 0;
        for ($i=0;$i<$length;$i++) {
            $a = $x[$i]-$mean1;
            $b = $y[$i]-$mean2;
            $axb = $axb+($a*$b);
            $a2 = $a2+ pow($a,2);
            $b2 = $b2+ pow($b,2);
        }
        if ( sqrt($a2*$b2) == 0 ) $corr = NULL;
        else $corr= $axb / sqrt($a2*$b2);
        return $corr;
    }

    private function kendallsTau ($x, $y){
        $numC = (int) 0;
        $numD = (int) 0;
        $tiesX = (int) 0;
        $tiesY = (int) 0;
        $numerator = (int) 0;
        $n = count($x);

        if ($n !== count($y) || $n === 0) {
            return $corr = NULL;
        }
        else {
            
            for ($i=0;$i<$n;$i++) {
            for ($j=$i+1;$j<$n;$j++) {
                
                if (  $x[$i] === $x[$j]  || $y[$i] === $y[$j]) {
                    if ( $x[$i] === $x[$j]  ) $tiesX++;
                    if ( $y[$i] === $y[$j]  ) $tiesY++;
                    continue;
                }
                
                if ( ($x[$i] < $x[$j] && $y[$i] < $y[$j]) || ($x[$i] > $x[$j] && $y[$i] > $y[$j]) ) {
                    $numC ++;   
                } else {
                    $numD ++;
                }
                
            }
            }
            
            $numerator = $n*($n-1)/2;
            //echo "Num$numerator C$numC D$numD  N$n";
            
            return $corr = ($numerator == 0 ? NULL : ($numC - $numD)/$numerator);
        }
        
        
    }
    
    private function spearmansRho ($x,$y) {
        usort($x);
        usort($y);
        return $this->pearsonCorrelation($x,$y);
        
    }

    
    
    
    
    
    
    
    
    
    
    
}


