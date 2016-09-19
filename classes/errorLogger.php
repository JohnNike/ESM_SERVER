<?php if(!defined('INDEX')){exit;}

class errorLogger {
	private $logDir;

	public function __construct($logDir){
		$this->logDir = $logDir;
	}

	public function log($msg){
		echo "LOG: $msg\n";
	}

}
