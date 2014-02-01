<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="styles.css" />

</head>
<body>
<?php
	require 'functions.php';
	set_attitude($_GET['p'], $_GET['a']);

for ($id = 1; $id <= 30; $id++) {
	$_text = mysql_query("SELECT text FROM propositions WHERE p = $id");
	if ($_text) {
		$text = mysql_fetch_row($_text); 
		print "<h3>$text[0]</h3>";
	}
	$_a = mysql_query("SELECT b, c FROM attitudes WHERE u = $u AND p = $id");
	if ($_a) {
		$a = mysql_fetch_row($_a);
		if ($a[0] == ACCEPT) {
			print "<p><em>You accept this</em></p>";
		} else if ($a[0] == REJECT) {
			print "<p><em>You reject this</em></p>";
		} else if ($a[1]) {
			print "<p><em>You are committed to this</em></p>";
		}
	}
}
?>
</body>
</html>