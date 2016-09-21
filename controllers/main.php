<?php if(!defined('INDEX')){exit;}


// JnPHP Controller Class

class main extends JnPHP {
	private $user;

	// cant user this counstruct since autoloading hasn't taken place yet
	function __construct(){

	}

	function main() {
		global $config, $_JnRequest;

		if( isset($_SESSION['user']) != "" ) {
			$this->user = $this->userModel->fetch($_SESSION['user']);
		} else {
			$this->user = null;
			$this->user = $this->userModel->fetch($_JnRequest[0]);
		}


		// Load an example model
		//$this->loadModel('exampleModel');
		// Fetch test content from our example model
		$content['text'] = $this->exampleModel->defaultContent();
		$content['config'] = $config;
		if ($this->user != null) {
			$content['text'] .= $this->user['userName'];
		}
//		$content['text'] .=
		// Load an example view
		$this->loadView('main',$content);

	}


}
