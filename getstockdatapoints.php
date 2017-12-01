<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/json');
ini_set('memory_limit','1024M');
if(isset($_GET['symbol']) && isset($_GET['tdate'])){
    $stock = $_GET['symbol'];
    $tdate = $_GET['tdate'];
}else{
    $stock = null;
    $tdate = null;
}

$options = array(
  'location' => 'http://localhost/mwatch/mwatch.php',
  'uri'      => 'http://localhost/mwatch/');
$client = new SoapClient(null, $options);

$data = $client->getstockdatapoints($stock,$tdate);
echo $data;
?>
