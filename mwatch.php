<?php
include_once("db.php");
include_once("dbconfig.php");
include_once("renderer.php");
include_once("log.php");


class marketwatch{
  //property region
  private $startdate;
  private $enddate;
  private $curdate;
  private $symbol;

  public function _construct(){}
  public function datapoints($symbol,$date1,$date2){
    $dpQuery="SELECT   `pricelist`.`StockCode`,`pricelist`.`QDate`,`pricelist`.`POpen`,";
    $dpQuery.="`pricelist`.`PClose`,`pricelist`.`PHigh`,`pricelist`.`PLow`";
    $dpQuery.="FROM     `pricelist`";
    $dpQuery.="WHERE( `pricelist`.`StockCode` = '{$symbol}' ) AND ( `pricelist`.`QDate` between  '{$date1}' AND '{$date2}' )";
    $dpD = new db();
    $dpData = $dpD->getRowAssoc($dpQuery);
    $data_points = array();
    for($f=0;$f<sizeof($dpData);$f++){
      $DStr = explode("-",$dpData[$f]['QDate']);
      $DateStr = "new Date(".$DStr[0].", ".$DStr[1].", ".$DStr[2].")";
      $PointStr = array();
      array_push($PointStr,$dpData[$f]['POpen']);
      array_push($PointStr,$dpData[$f]['PHigh']);
      array_push($PointStr,$dpData[$f]['PLow']);
      array_push($PointStr,$dpData[$f]['PClose']);
      $point = array("x" => str_replace('"','',$DateStr) , "y" =>$PointStr );
      array_push($data_points, $point); 
    }
    return json_encode($data_points, JSON_NUMERIC_CHECK);
    //return $data_points;
    /*$rendpt = new renderer();
    return $rendpt->render('json',$data_points);*/
  }
  public function getstockdatapoints($sym=null,$transdate=null){
    if(is_null($transdate)){
      $this->makeDateRange($this->setfCurDate());
    }else{
      $this->makeDateRange($this->setfDate($transdate));
    }
    if(is_null($sym)){
      $this->setInitSymbol();
    }else{
      $this->setSymbol($sym);
    }
    $gsdQuery="SELECT   `pricelist`.`StockCode`,`pricelist`.`QDate`,`pricelist`.`POpen`,";
    $gsdQuery.="`pricelist`.`PClose`,`pricelist`.`PHigh`,`pricelist`.`PLow`";
    $gsdQuery.="FROM     `pricelist`";
    $gsdQuery.="WHERE( `pricelist`.`StockCode` = '{$this->symbol}' ) AND ( `pricelist`.`QDate` between  '{$this->startdate}' AND '{$this->enddate}' )";
    $gsdD = new db();
    $gsdData = $gsdD->getRowAssoc($gsdQuery);
    $rendgsd = new renderer();
    return $rendgsd->render('json',$gsdData,'SERIES,<datapoint></datapoint>');
  }
  public function getstocks(){
    $sQuery="select distinct(StockCode) from pricelist ORDER BY StockCode";
    $sD = new db();
    $sData = $sD->getRowAssoc($sQuery);
    $rends = new renderer();
    return $rends->render('json',$sData,'SYMBOL,<coy></coy>');
  }
  public function topvolume($vdate=null){
    if(is_null($vdate)){
      $this->setfCurDate();
    }else{
      $this->setfDate($vdate);
    }
    $vQuery="SELECT   `pricelist`.`StockCode`, `pricelist`.`PTrades`,";
    $vQuery.="`pricelist`.`PVolume`,`pricelist`.`QDate`";
    $vQuery.="FROM     `pricelist`";
    $vQuery.="WHERE    ( `pricelist`.`QDate` ='{$this->curdate}'  )";
    $vQuery.="ORDER BY `pricelist`.`PVolume` DESC";
    $vD = new db();
    $vData = $vD->getRowAssoc($vQuery);
    $rendv = new renderer();
    return $rendv->render('json',$vData,'TOPVOLUME,<symbol></symbol>');
  }
  public function topvalue($tdate=null){
    if(is_null($tdate)){
      $this->setfCurDate();
    }else{
      $this->setfDate($tdate);
    }
    $tQuery="SELECT   `pricelist`.`StockCode`, `pricelist`.`RPrice`,";
    $tQuery.="`pricelist`.`PVolume`,`pricelist`.`PValue`,`pricelist`.`QDate`";
    $tQuery.="FROM     `pricelist`";
    $tQuery.="WHERE    ( `pricelist`.`QDate` ='{$this->curdate}'  )";
    $tQuery.="ORDER BY `pricelist`.`PValue` DESC";
    $tD = new db();
    $tData = $tD->getRowAssoc($tQuery);
    $rendt = new renderer();
    return $rendt->render('json',$tData,'TOPVALUE,<symbol></symbol>');
  }
  public function marketindex($idate=null){
    if(is_null($idate)){
      $this->setfCurDate();
    }else{
      $this->setfDate($idate);
    }
    $iQuery="SELECT   SUM(PVolume) as volumetraded, SUM(PValue) as capitalization,";
    $iQuery .="SUM(PTrades) as deals, `pricelist`.`QDate` FROM     `pricelist`";
    $iQuery .="WHERE    ( pricelist.QDate='{$this->curdate}' )";
    $iD = new db();
    $iData = $iD->getRowAssoc($iQuery);
    $rendi = new renderer();
    return $rendi->render('json',$iData,'MarketIndex,<snapshot></snapshot>');
  }
  public function toplosers($ldate=null){
    if(is_null($ldate)){
      $this->setfCurDate();
      }else{
      $this->setfDate($ldate);
    }
    $lQuery = "SELECT   `pricelist`.`QDate`, `pricelist`.`StockCode`,`pricelist`.`POpen`,`pricelist`.`PClose`,`pricelist`.`PChange`";
    $lQuery .= "FROM     `pricelist` WHERE    ( `pricelist`.`QDate` ='{$this->curdate}'  ) AND ( `pricelist`.`StockCode` not like 'NSE%' )";
    $lQuery .= "ORDER BY `pricelist`.`PChange` ASC LIMIT 10";
    $lD = new db();
    $lData = $lD->getRowAssoc($lQuery);
    $rendl = new renderer();
    return $rendl->render('json',$lData,'Losers,<symbol></symbol>');
  }
  public function topgainers($gdate=null){
    if(is_null($gdate)){
      $this->setfCurDate();
      }else{
      $this->setfDate($gdate);
    }
    $gQuery = "SELECT   `pricelist`.`QDate`, `pricelist`.`StockCode`,`pricelist`.`POpen`,`pricelist`.`PClose`,`pricelist`.`PChange`";
    $gQuery .= "FROM     `pricelist` WHERE    ( `pricelist`.`QDate` ='{$this->curdate}'  ) AND ( `pricelist`.`StockCode` not like 'NSE%' )";
    $gQuery .= "ORDER BY `pricelist`.`PChange` DESC LIMIT 10";
    $gD = new db();
    $gData = $gD->getRowAssoc($gQuery);
    $rend = new renderer();
    return $rend->render('json',$gData,'Gainers,<symbol></symbol>');
  }
  public function pricelist($fdate=null){
    if(is_null($fdate)){
      $this->setfCurDate();
    }else{
      $this->setfDate($fdate);
    }
        //$pQuery = "select * from pricelist where QDate = '{$this->curdate}'";
        $pQuery = "SELECT   `pricelist`.* FROM     `pricelist` WHERE    ( `pricelist`.`QDate` ='{$this->curdate}'  )";
	       //return $pQuery;exit;
        $pD = new db();
        $pData = $pD->getRowAssoc($pQuery);
        $rendr = new renderer();
        return $rendr->render('json',$pData,'Pricelist,<quote></quote>');
		//return $this->curdate;
  }
  private function setfDate($d){
    $this->curdate = $d;
  }
  private function setSymbol($tSym){
    $this->symbol = $tSym;
  }
  private function setInitSymbol(){
    $itQuery = "select distinct(StockCode) from pricelist ORDER BY StockCode desc limit 1";
    $itD = new db();
    $itData = $itD->getRowAssoc($itQuery)[0][0];
    $this->symbol = $itData;
  }
  private function setfDateRange($sd,$ed){
    $this->startdate = $sd;
    $this->enddate = $ed;
  }
  private function getCurDate(){
    $dQuery = "select QDate from pricelist order by QDate desc";
    $dQ = new db();
    $dD = $dQ->getRows($dQuery)[0][0];
    return $dD;
  }
  private function setfCurDate()
  {
    $df = $this->getCurDate();
    $this->curdate = $df;
  }
  private function makeDateRange(){
    $ind = $this->getCurDate();
    $dPair = explode("-",$ind);
    $prevY = $dPair[0] - 1;
    $prev = $prevY."-".$dPair[1]."-".$dPair[2];
    $this->enddate = $ind;
    $this->startdate = $prev;
  }
}

$options = array('uri' => 'http://localhost:8080/mwatch/');
$SOAPServer = new SoapServer(null, $options);
$SOAPServer->setClass('marketwatch');
$SOAPServer->handle();
?>
