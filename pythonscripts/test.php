<?php

        $pyFile = 'mic.py'; 
        $pyDir = dirname(__FILE__,2).'/pythonscripts';
        
        $toR = [
            'y1' => [1,3,5,6,7],
            'y2' => [2,4,4,9,9]
            ];
        
        $shellArgs = escapeshellarg(json_encode($toR));
        // exec("source /var/www/correlation/python_api/py/flask/venv/bin/activate $pyDir/$pyFile 2>&1 $pyDir $shellArgs",$sh);

        exec(". /var/www/correlation/python_api/py/flask/venv/bin/activate && python $pyDir/$pyFile 2>&1 $pyDir $shellArgs",$sh);
        var_dump($sh);
        $res = json_decode($sh[0],TRUE);
        
        print_r($res);
        echo round($res['py_exec_time'],1);
        return $res['mic'];