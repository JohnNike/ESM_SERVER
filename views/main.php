<?php

$gCurrLang = 'el';

$viewTranslations = array(
	'VIEW_TITLE' => array('el'=>'Αρχική','en'=>'Home'),
	'VIEW_DESCRIPTION' => array('el'=>'Αρχική','en'=>'Home'),
);

$VIEW_TITLE = $viewTranslations['VIEW_TITLE'][$gCurrLang];
$VIEW_DESCRIPTION = $viewTranslations['VIEW_DESCRIPTION'][$gCurrLang];
$VIEW_STYLES = '';

include(TEMPLATEPATH.'header.php');
include(TEMPLATEPATH.'content.php');
echo "<code>";
echo "Retrieved by view:" . $text;
echo "</code>";

include(TEMPLATEPATH.'footer.php');
