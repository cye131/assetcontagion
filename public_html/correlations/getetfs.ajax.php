<?php
    require_once(dirname(dirname(__FILE__)).'/../config/db.php'); //Single Nest: include(dirname(__FILE__). '/../config/db.php'); //Double Nest: require_once(dirname(dirname(__FILE__)).'/../config/db.php');
    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


    $stmt = $conn->prepare(
                           "SELECT * FROM `sector_sectoretfmatch` WHERE `classification_type` = 'gics' AND
                           (
                            (`classification_level` = 'sector' AND `sector_name` = ?) OR
                            (`classification_level` = 'industry' AND `sector_name` = ?) OR
                            (`classification_level` = 'subindustry' AND `sector_name` = ?)
                            )"
                          );

    $sector = $_POST['sector'];
    $industry = $_POST['industry'];
    $subindustry = $_POST['subindustry'];    
        
    $stmt->bind_param("sss", $sector, $industry, $subindustry);
    $stmt->execute();
    
    $res = $stmt->get_result();
    
    if (empty($res)) {
      echo json_encode("{Error - no sector information available on that stock ticker.}");
      exit();
    }
    
    $assoc = array();
    while ($row = $res->fetch_assoc()) {
        $assoc[$row['classification_level']] = array();
        $assoc[$row['classification_level']]['name'] = $row['sector_name'];
        $assoc[$row['classification_level']]['lookup_code'] = $row['classification_code'];
    }
    
    $stmt->close();
    
    if (empty($assoc)) {
      echo json_encode("{Error - no sector information available on that stock ticker.}");
      exit();
    } else {
      $assoc['stock'] = array('name'=>$_POST['name'],'lookup_code' => $_POST['ticker']);
      $assoc['market'] = array('name'=>'S&P 500','lookup_code' => '.SPX');
      echo json_encode($assoc);
    }
  ?>