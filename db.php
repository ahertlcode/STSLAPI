<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db
 *
 * @author abayomi
 */

/*Define Constant to old DB Parameters*/
class db extends mysqli{

private $connerror = "Database Connection Fail";
private $conn;
public $inQuery;
private $outcome;
private $tableRow = array();
private $tableAssoc = array();
private $erno;
private $errmsg;
private $low;

//Connect to MySQL
public function _construct()
{

}
private function makeCon()
{
	if(!($this->conn = new mysqli($this->dbhost,$this->dbuser,$this->dbpass,$this->dbname)))
    {
        $this->erno = mysqli_connect_errno();
        $this->errmsg = mysqli_connect_error();
        $this->logError();
        return $this->connerror;
    }
    return $this->conn;
	}
public function executeQuery($q)
{
    $dbcon = $this->makeCon();
    $this->inQuery = $q;
    $this->outcome = $dbcon->query($this->inQuery);
    if(!$this->outcome){
        $this->erno = $dbcon->errno;
        $this->errmsg = $dbcon->error;
        $this->logError();
        return 0;
    }
    $dbcon->close();
    return $this->outcome;
}
public function getRows($q)
{
    $dbh = $this->makeCon();
	$this->tableRow = (array) null;
    $this->inQuery = $q;
    $this->outcome = $dbh->query($this->inQuery);
    if(!$this->outcome){
        $this->erno = $dbh->errno;
        $this->errmsg = $dbh->error;
        $this->logError();
        return 0;
    }
    while($this->low = $this->outcome->fetch_row())
    {
        $this->tableRow[] = $this->low;
    }
    $dbh->close();
    return $this->tableRow;
}
public function getRowAssoc($q)
{
    $dh = $this->makeCon();
	$this->tableAssoc = (array) null;
    $this->inQuery = $q;
    $this->outcome = $dh->query($this->inQuery);
    if(!$this->outcome){
        $this->erno = $dh->errno;
        $this->errmsg = $dh->error;
        $this->logError();
        return 0;
    }
    while($this->low = $this->outcome->fetch_assoc())
    {
        $this->tableAssoc[] = $this->low;
    }
    $dh->close();
    return $this->tableAssoc;
}
private function logError()
{
    $fp = fopen("Error/ErrorLog.txt","a+");
    $msg = date("Y-m-d H:s i")." : "."Database Error No: ".$this->erno." - "."Error Information: ".$this->errmsg."\n";
    fwrite($fp,$msg);
    fclose($fp);
}
}
