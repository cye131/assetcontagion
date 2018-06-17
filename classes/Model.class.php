<?php

class Model {
    
    public function __construct(string $file, array $varsToSend, array $varsToReturn) {
        $this->file = $file;
        $this->varsToSend = $varsToSend;
        $this->varsToReturn = $varsToReturn;
        $this->get();
    }
        
    public function get() {
        $fromRouter = [];

        foreach ($this->varsToSend as $v => $k) {
            $fromRouter[$k] = $v;
        }
        
        $sql = new MyPDO();
        require_once("/var/www/correlation/models/{$this->file}.model.php");
        
        //Set varsToReturn as class properties of self
        foreach ($this->varsToReturn as $varToReturn) {
            $this->$varToReturn = ${$varToReturn} ?? NULL;
        }
        
    }
    

    
}