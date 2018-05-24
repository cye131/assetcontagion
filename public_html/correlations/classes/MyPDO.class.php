<?php
class MyPDO extends PDO {
    
    public function __construct($dsn, $username = null, $password = null, array $driver_options = null) {
         parent :: __construct($dsn, $username, $password, $driver_options);
         $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

}

?>