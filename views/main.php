<?php if(!defined('INDEX')){exit;}

$gCurrLang = 'el';

$viewTranslations = array(
	'VIEW_TITLE' => array('el'=>'Αρχική','en'=>'Home'),
	'VIEW_DESCRIPTION' => array('el'=>'Αρχική','en'=>'Home'),
);

$VIEW_TITLE = $viewTranslations['VIEW_TITLE'][$gCurrLang];
$VIEW_DESCRIPTION = $viewTranslations['VIEW_DESCRIPTION'][$gCurrLang];
$VIEW_STYLES = '';

include(TEMPLATEPATH.'main_template.php');
include(TEMPLATEPATH.'main_lqyout.php');

