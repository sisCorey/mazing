<?php

function classInclude($class)
{
	$path = '.';
	$classPath = $path.'/'.$class.'.class.php';
	if (file_exists($classPath)) {
		require_once($classPath);
	}
}

function ethrow($msg, $code = 0)
{
	throw new Exception($msg, $code);
}

spl_autoload_register('classInclude');
