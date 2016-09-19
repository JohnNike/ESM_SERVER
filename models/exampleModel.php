<?php if(!defined('INDEX')){exit;}

class exampleModel extends JnPHP {

	function __construct() {
	}

	function defaultContent() {
		$text = 'Model Produced Text.';
		return $text;
	}

	function  jsonContent() {
		return "could send json";
	}

}
