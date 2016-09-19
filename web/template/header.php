<?php

$HEAD = '
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>'. $VIEW_TITLE .'</title>

		<meta name="description" content="' .$VIEW_DESCRIPTION .'" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<meta name="author" content="'.$config['author'].'" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="web/assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="web/assets/font-awesome/4.2.0/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->
		'.$VIEW_STYLES.'

		<!-- text fonts -->
		<link rel="stylesheet" href="web/assets/fonts/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="web/assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="web/assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="web/assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="web/assets/js/ace-extra.min.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="web/assets/js/html5shiv.min.js"></script>
		<script src="web/assets/js/respond.min.js"></script>
		<![endif]-->
	</head>
';
echo $HEAD;