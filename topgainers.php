<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/json');
ini_set('memory_limit','1024M');

if(isset($_GET['d'])){
  $param = $_GET['d'];
}else{
  $param = null;
}
$options = array(
  'location' => 'http://localhost/mwatch/mwatch.php',
  'uri'      => 'http://localhost/mwatch/');
$client = new SoapClient(null, $options);

$data = $client->topgainers($param);
echo $data;
?>
