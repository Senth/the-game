<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd>
<?php $this->load->helper('asset'); ?>

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Matteus Magnusson">
	<title>The Game</title>
	<?php
echo css_asset('style.css') . "\n";
echo css_asset('dark-hive/jquery-ui-1.8.20.custom.css') . "\n";
echo js_asset('jquery-1.7.2.min.js') . "\n";
echo js_asset('jquery-ui-1.8.20.custom.min.js') . "\n";
echo js_asset('forms.js') . "\n";
echo js_asset('messages.js') . "\n";
echo js_asset('debugging.js') . "\n";
echo js_asset('event_handler.js') . "\n";
echo js_asset('ajax_forms.js') . "\n";
?>
</head>
<body>
<div id="wrap">
<div id="header">
<div id="logo"></div>
</div>
