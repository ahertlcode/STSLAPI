<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/json');
ini_set('memory_limit','1024M');

$jsonob = file_get_contents("http://etrade.santrustsecurities.com.ng/nsemarketdataapi.svc/Snapshot");
$obj = json_decode($jsonob);
var_export($obj->AllShareIndex);
$AllShareIndex = $obj->AllShareIndex;
echo "\n";
echo "\n";
echo "\n";
echo $AllShareIndex->SecurityDescription;
echo " for ";
echo $AllShareIndex->LongUpdated;
echo "\n";
echo "All Share Index(ASI): ".$AllShareIndex->ClosingPrice;
echo "\n Market Capitalization: ";
echo "\n Market Value: ".$AllShareIndex->TotalValueOfSecurityTradedToday;
echo "\n Market Volume: ".$AllShareIndex->TotalNumberOfSharesTradedToday;
echo "\n Deals: ".$AllShareIndex->TotalNumberOfTradesToday;
exit;
if(isset($_GET['d'])){
  $param = $_GET['d'];
}else{
  $param = null;
}
$options = array(
  'location' => 'http://localhost/mwatch/mwatch.php',
  'uri'      => 'http://localhost/mwatch/');
$client = new SoapClient(null, $options);

$data = $client->marketindex($param);
echo $data;
?>
