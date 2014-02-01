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
?>
<div id="page">
	<?php
	$id = $_GET['id'];
	$maxid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM arguments"));
	if (!($id > 0 && $id <= $maxid)) {
		print '<p>An invalid argument ID was sent</p>';
		return;
	}
	$arg_data =  mysql_fetch_row(mysql_query("SELECT subargs, name, contextpt,source,source_author FROM arguments WHERE id = $id"));
	?>
	
	<h1><?php print $arg_data[1]; ?></h1>
	
	<?php if ($arg_data[3] || $arg_data[4]) { ?>
		<div id="arg-source">
			<span class="inline-label">Source:</span>
			<?php
			print $arg_data[4];
			if ($arg_data[3] && $arg_data[4]) {
				print ', ';
			}
			print $arg_data[3];
			?>
		</div>
	<?php } ?>
	
	<?php if ($arg_data[2]) { ?>
		<div id="arg-info">
			<?php
			$arg_text = mysql_fetch_row(mysql_query("SELECT text FROM arg_text WHERE id = $arg_data[2]"));
			print $arg_text[0];
			?>
		</div>
	<?php } ?>

	<?php
	$is = explode(',', $arg_data[0]);
	$argpnum = 1;
	foreach ($is as $i) {
		$fromtext = '<span class="from">(From ';
		$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i");
		if ($_ps) {
			$ps = mysql_fetch_row($_ps);
			$cid = array_pop($ps);
			$pnum = 1;
			foreach ($ps as $p) { // PROPOSITIONS, OTHER THAN CONCLUSION
				if ($p) {
					$PDONE = FALSE;
					for ($oldpnum = $argpnum - 1; $oldpnum >= 1; $oldpnum--) {
						if ($a[$oldpnum][p] == $p) {
							$fromtext .= $oldpnum.' ';
							$PDONE = TRUE;
						}
					}
					if (!$PDONE) {
						$fromtext .= $argpnum.' ';
						$a[$argpnum][p] = $p;
						$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $p AND u=$u");
						$pa = mysql_fetch_row($_pa);
						if ($pa[0] != NULL && $pa[0] != "NULL") {
							$a[$argpnum][b] = $pa[0];
						} else {
							$a[$argpnum][b] = NULL;
						}
						if ($pa[1] != NULL && $pa[1] != "NULL") {
							$a[$argpnum][c] = $pa[1];
						} else {
							$a[$argpnum][c] = NULL;
						}
						// Print the proposition line 
						print_buttons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
						print_proposition($p, $argpnum);
						$argpnum++;
					}
				}
			}
			// THE CONCLUSION
			$a[$argpnum][p] = $cid;
			$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $cid AND u=$u");
			$pa = mysql_fetch_row($_pa);
			if ($pa[0] != NULL && $pa[0] != "NULL") {
				$a[$argpnum][b] = $pa[0];
			} else {
				$a[$argpnum][b] = NULL;
			}
			if ($pa[1] != NULL && $pa[1] != "NULL") {
				$a[$argpnum][c] = $pa[1];
			} else {
				$a[$argpnum][c] = NULL;
			}
			// Print the conclusion line 
			print_buttons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
			print_proposition($cid, $argpnum, TRUE);
			$argpnum++;
		}
	} ?>
</div>
</body>
</html>