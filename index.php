<!DOCTYPE html>
<html>
<head>
	<title>mazing</title>
	<style type="text/css">
	* {margin: 0;padding: 0;font-size: 12px;}
	div.l {height: 3px;}
	span.p {display: inline-block; width: 3px; height: 3px; outline: #777 0px solid; box-sizing: border-box; vertical-align: top;}
	span.p_0 {background: #fff;}
	span.p_1 {background: #43a6fb;}
	span.p_w {background: #000;}
	span.p_s {background: red;}
	span.p_e {background: blue;}
	span.p_o {background: yellow;}
	</style>
</head>
<body>
<?php

require_once('route.php');

$m = new Main;
$m->start();

?>
</body>
</html>