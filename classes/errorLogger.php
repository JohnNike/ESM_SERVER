<?php if(!defined('INDEX')){exit;}

class errorLogger {
	private $logDir;

	public function __construct($logDir = ERROR_LOG_DIR){
		$this->logDir = $logDir;
	}

	public function log($msg){
		//echo "LOG: $msg\n";
		file_put_contents(ERROR_LOG_DIR, $msg, FILE_APPEND);
	}


}
