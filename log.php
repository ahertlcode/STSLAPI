<?php

class log{
	private $emptyType = "Error type must be specified";
	private $emptyMsg = "Error message must be indicated, i.e. cannot be left Empty or null";
	private $ErrTp;
	private $ErrMsg;
	private $ErrIcon;


	public function _construct(){}

	public function RaiseError($ErrorType, $ErrorMessage, $ErrorIcon = null)
	{
		if(is_null($ErrorType)){
			return $this->emptyType;
		}else if(is_null($ErrorMessage)){
			return $this->emptyMsg;
		}else{
			$this->setError($ErrorType,$ErrorMessage,$ErrorIcon);
		}
	}

	private function setError($tp,$msg,$icon){
			$this->ErrTp = $tp;
			$this->ErrMsg = $msg;
			$this->ErrIcon = $icon;
			$this->WriteError();
	}

	private function WriteError(){
		$ErrorStr = "Logged Error:<br/>";
		$ErrorStr .= date("Y-m-d H:s i");
		$ErrorStr .= "\t {$this->ErrTp}: \t {$this->ErrMsg}\n";
		$fp = fopen("Error/ErrorLog.txt","a+");
		fwrite($fp,$ErrorStr);
		fclose($fp);
	}
}
?>
