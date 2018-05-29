<?php
class MyPDO extends PDO {
    
    
    public function __construct() {
        
        require_once('/var/www/correlation/config/db.php');
        
        $dsn = 'mysql:dbname='.DB_DATABASE;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $driver_options = null;
        
        parent :: __construct($dsn, $username, $password, $driver_options);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
}

?>