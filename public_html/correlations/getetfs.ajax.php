<?php
    spl_autoload_register('myAutoloader');
    function myAutoloader($classname) {
      require_once "/var/www/correlation/public_html/correlations/classes/" . $classname . '.class.php';
    }
    $sql = new MyPDO();

    $stmt = $sql->prepare(
                           "SELECT * FROM `fid_tickers_sectors` WHERE
                           (
                            (`classification_level` = 'sector' AND `sector_name` = :sector) OR
                            (`classification_level` = 'industry' AND `sector_name` = :industry) OR
                            (`classification_level` = 'subindustry' AND `sector_name` = :subindustry)
                            )"
                          );

    $sector = $_POST['sector'];
    $industry = $_POST['industry'];
    $subindustry = $_POST['subindustry'];    

    $stmt->bindParam(':sector', $sector);
    $stmt->bindParam(':industry', $industry);
    $stmt->bindParam(':subindustry', $subindustry);
    
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
      echo json_encode("{Error - no sector information available on that stock ticker.}");
      exit();
    }
    
    $assoc = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $assoc[$row['classification_level']] = array();
        $assoc[$row['classification_level']]['name'] = $row['sector_name'];
        $assoc[$row['classification_level']]['lookup_code'] = $row['classification_code'];
    }
        
    $assoc['stock'] = array('name'=>$_POST['name'],'lookup_code' => $_POST['ticker']);
    $assoc['market'] = array('name'=>'S&P 500','lookup_code' => '.SPX');
    
    echo json_encode($assoc);
  ?>