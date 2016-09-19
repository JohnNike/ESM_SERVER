<?php if(!defined('INDEX')){exit;}


// JnPHP Controller Class

class main extends JnPHP {

	function __construct(){
	}

	function main() {
		global $config;

		// Load an example model
		//$this->loadModel('exampleModel');
		// Fetch test content from our example model
		$content['text'] = $this->exampleModel->defaultContent();
		$content['config'] = $config;


		// Load an example view
		$this->loadView('main',$content);
	}


}
