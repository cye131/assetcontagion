<?php
  require_once(dirname(dirname(__FILE__)).'/../config/db.php'); //Single Nest: include(dirname(__FILE__). '/../config/db.php'); //Double Nest: require_once(dirname(dirname(__FILE__)).'/../config/db.php');
  $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
  if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
  
  $stmt = $conn->prepare("SELECT * FROM `sector_stocksectormatch` WHERE `ticker` = ? AND `classification_type` = 'gics'");
  $stock = $_POST['stock'];

  $stmt->bind_param("s", $stock);
  $stmt->execute();

  $res = $stmt->get_result();
  
  $assoc = mysqli_fetch_assoc($res);
  
  $stmt->close();
  
  
  if (empty($assoc)) echo json_encode(array('error' => 'Sorry - No Information Found'));
  else echo json_encode($assoc);
    
  ?>