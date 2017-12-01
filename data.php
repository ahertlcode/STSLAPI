<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/json');
ini_set('memory_limit','1024M');

if(isset($_GET['sym']) && isset($_GET['sdate']) && isset($_GET['edate'])){
    $stock = $_GET['sym'];
    $tdate = $_GET['sdate'];
    $ldate = $_GET['edate'];
}else{
    $stock = null;
    $tdate = null;
    $ldate = null;
}

$options = array(
  'location' => 'http://localhost:8080/mwatch/mwatch.php',
  'uri'      => 'http://localhost:8080/mwatch/');
$client = new SoapClient(null, $options);

$data = $client->datapoints($stock,$ldate,$tdate);
echo $data;
?>