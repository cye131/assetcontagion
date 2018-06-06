<?php
/**
 * -----------------------------------------------------------------------------------------
 * Slightly modified version of https://gist.github.com/Rodrigo54/93169db48194d470188f
 * -----------------------------------------------------------------------------------------
 */
Class TestOutput {
    
    public function __construct ($input) {
        $this->input = $input;
    }
    
    public function print () {
        $file_parts = pathinfo($_SERVER['REQUEST_URI']); 
        if (!isset($file_parts['extension']) ) return;
        
        if (is_array($this->input)) {
            echo '<div style="max-height:400px;overflow-y: scroll"><pre>';
            print_r($this->input);
            echo '</pre></div>';
        } else {
            echo '<br>'.$this->input;
        }
        
        
    }
    
    
    
}