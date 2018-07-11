<?php
use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\LinearAlgebra\Vector;

class Timeseries {
    
    /* the data parameter takes an array of time series or 
     *
     *
     *
     */
    public function __construct(array $data, array $optional_dates = NULL,  string $optional_names = NULL, string $optional_start = NULL, string $optional_end = NULL, string $optional_freq = NULL,  string $optional_deltat = NULL) {
        $this->_validateInputs ($data, $optional_dates, $optional_names, $optional_start, $optional_end, $optional_freq, $optional_deltat);
    }
    
    /* parameter checking
     *
     *
     *
     */
    protected function _validateInputs( $data, $optional_dates, $optional_names, $optional_start, $optional_end, $optional_freq, $optional_deltat) {
        $argGuide = '';
        
        if ( count($data) === 0 ) throw new InvalidArgumentException ('$values vector cannot be empty!');
        
        if ( $optional_dates != false  && count($optional_dates) == 0 ) throw new InvalidArgumentException ('if the $optional_dates argument is set (use NULL if you do not wish to set this), it cannot be an empty vector. ');

        if ( $optional_dates != false  && count($data) !== count($optional_dates) ) throw new InvalidArgumentException ('if the $optional_dates argument is set (use NULL if you do not wish to set this), it must have a count equal to the $values vector. ');

        
        $this -> data = $data;
        
        if ( $optional_dates ) {
            $this -> dates = $optional_dates;
        } else {
            //calculation of dates based off other parameters
        }
        /*
        $this-> dataframe = [];
        for ($i=0; $i<count($data);$i++) {
            $this-> dataframe[$i] = [
                0 => $this-> dates[$i],
                1 => $this-> data[$i]
            ];
        }*/
    
    }
    
    
    

    
    
    /* Autocorrelation Function
     *  Data must be sorted by date ascending
     * 
     *
     */
    public  function acf() {
        $y = $this->data;
    

        $N = count($y);
        $ybar = array_sum($y)/$N;

        $rho = [];
        
        //$k is the lag -> will run from 1 to 
/*        for ($k = 1; $k <= $N-1 ; $k++ ) {*/
        for ($k = 0; $k <= $N - 1; $k++ ) {

            $denom = 0;
            for ($i=0;$i<=$N-1;$i++) {
                $denom +=  pow( $y[$i] - $ybar, 2 );
            }
            if ($denom === 0) continue;
            
            $num = 0;
            for ($i=0; $i<=$N-$k-1; $i++) {
                    $num += ( ($y[$i] - $ybar) * ($y[$i + $k] - $ybar) );  
            }
            
            
            $se = ($N-$k) !== 0 ? 1.96/sqrt($N-$k) : 0;
            
            $rho[] = [
                      0 => $k,
                      1 => round($num/$denom, 8),
                      2 => $se
                      ];            
            //https://stats.stackexchange.com/questions/185425/how-to-determine-the-critical-values-of-acf
        }
        
    return $rho;
        
        
    }
    
    
    public function pacf(int $MAX_LAG = 200) {

        $y = $this->data;
        $N = count($y);
        $ybar = array_sum($y)/$N;
        $ytilde = array_map(function($v) use ($ybar)  {return $v -$ybar; }, $y);
        
        $acf = $this->acf();
        $acf_vals = array_column($acf,1);    
        $acf_se = array_column($acf,2);
        
        
        
        
        $AVec = array_slice($acf_vals,0,$N-2);
        $BVec = array_slice($acf_vals,1,$N-1);
        
        if (count($AVec) > $MAX_LAG ) $AVec = array_splice($AVec,0,$MAX_LAG);
        if (count($BVec) > $MAX_LAG ) $BVec = array_splice($BVec,0,$MAX_LAG);


        $AMtx = $this::toeplitz($AVec);
        $AMtx -> inverse();
        $BMtx = new Vector($BVec);
                
        $AMtx -> vectorMultiply($BMtx);
        $SolMtx = $AMtx -> vectorMultiply($BMtx);
        $solArray = $SolMtx ->getVector();
        $lag = (int) 1;
        $res = [];
        foreach ($solArray as $a) {
            $res[$lag]['lag'] = $lag;
            $res[$lag]['pacf'] = $a;
            $res[$lag]['se'] = $acf_se[$lag];
            $lag++;
        }
        
        
        
        return json_encode($res);
    }
    
    
    
    
    public static function toeplitz($vector) {
        $matrixRows = [];
        
        for ($i=0;$i<count($vector);$i++) {

            for ($j=0;$j<count($vector);$j++) {
                $diff = abs($j-$i);
                $matrixRows[$i][$j] = $vector[$diff];
            }
        }
        return new Matrix($matrixRows);
        
    }
    
    
    
    
    
    
    
    
    
    
    public static function MIC($y1,$y2) {
        /*
        $rFile = 'mic.r'; 
        $rDir = dirname(__FILE__,2).'/rscripts';
        
        $toR = [
            'y1' => $y1,
            'y2' => $y2
            ];
        
        $shellArgs = escapeshellarg(json_encode($toR));
        
        exec("Rscript $rDir/$rFile 2>&1 $rDir $shellArgs",$sh);
        
        if (count($sh) !== 1) {
            echo 'ERROR: '.print_r($sh);
            return;
        }
        
        $res = json_decode($sh[0],TRUE);
        
        
        if ( !is_numeric($res['MIC'][0]) ){
            echo 'NON NUMERIC: '.print_r($sh);
            return;
        }
        
        
        echo 'R Exec Time: '.round($res['r_exec_time'][0],2).' | ';
        return $res['MIC'][0];
    */
        
        $pyFile = 'mic.py'; 
        $pyDir = dirname(__FILE__,2).'/pyscripts';
        $vEnv = dirname(__FILE__,2).'/pyenv/bin/activate';

        $toR = [
            'y1' => $y1,
            'y2' => $y2
            ];
        
        $shellArgs = escapeshellarg(json_encode($toR));
        exec(". $vEnv && python $pyDir/$pyFile 2>&1 $pyDir $shellArgs",$sh);
        if (count($sh) !== 1) {
            echo 'ERROR1: '.print_r($sh);
            return;
        }
        $res = json_decode($sh[0],TRUE);
        if (!$res['mic']) {
            echo 'ERROR2: '.print_r($sh);
            return;
        }
        
        //print_r($res);cro
        echo 'Python Exec Time: '.round($res['py_exec_time'],2).' | ';
        return $res['mic'];
        
    }
    
}