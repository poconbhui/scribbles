<?php
$pages = array(
	'gnuplot'=>'index.php',
	'php_gd'=>'gd.php',
	'c_gd'=>'gd_and_c.php',
	'c'=>'scribbuild.c'
	);

if(!($page = $pages[$_GET['p']]))
	{
	$page = 'index.php';
	}

$file=file_get_contents($page);

header('Content-Type: text/plain');
echo $file;
?>
