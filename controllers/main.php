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
			if (isset($_JnRequest[0]))
				$this->user = $this->userModel->fetch($_JnRequest[0]);
		}

		$content['config'] = $config;
		if ($this->user != null) {
			$content['text'] = $this->user['userName'];
		}
//		$content['text'] .=
		// Load an example view
		$this->loadView('main',$content);

	}


}
