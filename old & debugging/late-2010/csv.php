<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="styles.css" />

</head>
<body>
<?php
	require 'functions.php';
	$fp = fopen('ariane-utf.csv', 'rb');

while ($line = fgetcsv($fp)) {	
	$line_1 = addslashes($line[0]);
	$line_2 = addslashes($line[1]);
	$line_3 = addslashes($line[2]);
	$line_4 = addslashes($line[3]);
	mysql_query("INSERT INTO csv (u,p,b,c) VALUES ('$line_1', '$line_2', '$line_3', '$line_4')");
	print (mysql_error());
}
fclose($fp);
	
	
?>
</body>
</html>