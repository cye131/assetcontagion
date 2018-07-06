<?php

        $rFile = 'mic.r'; 
        $rDir = dirname(__FILE__,2).'/rscripts';
        
        $toR = [
            'y1' => [1,3,5],
            'y2' => [2,4,4]
            ];
        
        $shellArgs = escapeshellarg(json_encode($toR));
        
        exec("Rscript $rDir/$rFile 2>&1 $rDir $shellArgs",$sh);
        $res = json_decode($sh[0],TRUE);
        
        //print_r($res);
        echo $res['r_exec_time'][0];
        return $res['MIC'][0];

        
       