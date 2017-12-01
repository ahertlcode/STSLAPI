<?php

class renderer{
	private $rendered;
	private $fmt;
	private $lrootx;
	private $XmlNodes;
	public function _construct(){
	}
	public function render($format,$data,$options=null)
	{
		if(is_null($format))
		{
			$lg = new log();
			$lg->RaiseError('format','Please specify a format!');
		}
		else
		{
			$localMethod = "Render".strtoUpper($format);
			if(!is_null($options))
			{
				$this->SetOptions($options);
			}
			if(is_null($data))
			{
				$lg2 = new log();
				$lg2->RaiseError('data','Data must not be null');
			}
			else
			{
				return $this->$localMethod($data);
			}
		}
	}
	private function SetOptions($opt){
		$ch = explode(",",$opt);
		$this->XmlNodes = $ch[0];
		$this->lrootx = $ch[1];
	}
	private function RenderJSON($d)
	{
		return  json_encode(simplexml_load_string($this->RenderXML($d,$this->lrootx)));
	}

	private function RenderXML($ArrayX,$lroot=null,$xml=null)
	{
		$_xml = $xml;
		if($_xml === null){
			$_xml = new SimpleXMLElement($lroot !== null ? $lroot : '<root></root>');
			}
		foreach($ArrayX as $key=>$value){
			if(is_array($value)){
				if(is_numeric($key)){
						$key = $this->XmlNodes;
					}
				$this->RenderXML($value,$key,$_xml->addChild($key));
			}else{

					$_xml->addChild($key,$value);
				}
				}
				return $_xml->asXML();
	}
	private function RenderBSON($dbs) {}
	private function RenderODATA($dos) {}
	private function RenderYAML($dY){}
	private function RenderHTMLTABLE($dt){}
	private function RenderHTMLTABLESORTABLE($dtl){}
}
?>
