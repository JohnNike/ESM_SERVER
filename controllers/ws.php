<?php if(!defined('INDEX')){exit;}
/**
 * Created by PhpStorm.
 * User: john
 * Date: 14/9/16
 * Time: 5:23 μμ
 */

// JnPHP Controller Class
class ws extends JnPHP {

	function __construct(){
	}

	function main() {
		global $config;

		// Load an example model
		//$this->loadModel('exampleModel'); // it's autoloaded
		// Fetch test content from our example model

		// send an example response
		$this->sendResponse($this->exampleModel->jsonContent());
	}


}
