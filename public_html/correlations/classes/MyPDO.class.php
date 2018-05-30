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
    
    
    public function multipleInsert($tblName,$colNames,$dataVals) {
        //from https://stackoverflow.com/a/4559320
        
        // setup data values for PDO
        // memory warning: this is creating a copy all of $dataVals
        $dataToInsert = array();
        
        foreach ($dataVals as $row => $data) {
            foreach($data as $val) {
                $dataToInsert[] = $val;
            }
        }
        
        // (optional) setup the ON DUPLICATE column names
        $updateCols = array();
        
        foreach ($colNames as $curCol) {
            $updateCols[] = $curCol . " = VALUES($curCol)";
        }
        
        $onDup = implode(', ', $updateCols);
        
        // setup the placeholders - a fancy way to make the long "(?, ?, ?)..." string
        $rowPlaces = '(' . implode(', ', array_fill(0, count($colNames), '?')) . ')';
        $allPlaces = implode(', ', array_fill(0, count($dataVals), $rowPlaces));
        
        $this->beginTransaction();
        
        $sql = "INSERT INTO $tblName (" . implode(', ', $colNames) . 
            ") VALUES " . $allPlaces . " ON DUPLICATE KEY UPDATE $onDup";
        
        echo $sql;
        // and then the PHP PDO boilerplate
        $stmt = $this->prepare ($sql);
        
        try {
           $stmt->execute($dataToInsert);
        } catch (PDOException $e){
           echo $e->getMessage();
        }
        
        $this->commit();

    }
    
}

?>