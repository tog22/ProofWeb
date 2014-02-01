<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="styles.css" />

</head>
<body>
</body>
</html>
<?php
	require 'functions.php';
?>
<?php
for ($p = 1; $p <= 30; $p++) {
	$_lx = mysql_fetch_row(mysql_query("SELECT implications FROM propositions WHERE p = $p"));
		print_r($_lx);
		$lx = explode(',', $_lx[0]);
		$lknum = 1;
		foreach ($lx as $lk) {
			print "f";
				print $lk;
			$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $lk");
			if ($_ps) {
				$ps = mysql_fetch_row($_ps);
				$as[$lknum][conc] = array_pop($ps);
				$plknum =1;
				foreach ($ps as $plk) {
						$as[$lknum][$plknum][p] = $plk;
						$_text = mysql_query("SELECT text FROM propositions WHERE p = $plk");
						if ($_text) {
							$text = mysql_fetch_row($_text); 
							print "<p>$plk $text[0]</p>";
						}
						$plknum++;
				}
				$cid = $as[$lknum][conc];
				$_text = mysql_query("SELECT text FROM propositions WHERE p = $cid");
				if ($_text) {
					$text = mysql_fetch_row($_text); 
					print "<p><b>".$as[$lknum][conc]."</b> $text[0]</p>";
				}
			}
		}
}
?>