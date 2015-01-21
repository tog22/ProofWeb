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
	require 'functions/functions.php';
?>
<div id="page">
<?php
	$id = $_GET['id'];
	$dev = $_GET['dev'];
	$maxid = mysql_fetch_row(mysql_query("SELECT MAX(p) FROM propositions"));
	if (!($id > 0 && $id <= $maxid)) {
		print '<p>An invalid proposition ID was sent</p>';
		return;
	}
	$_pInfo = mysql_query("SELECT text, definitional, logical, contextpt FROM propositions WHERE p = $id");
	if ($_pInfo) {
		$pInfo = mysql_fetch_row($_pInfo);
	}
	define('TEXT', 0);
	define('IS_DEFINITIONAL', 1);
	define('IS_LOGICAL', 2);
	define('CONTEXT_ID', 3);
?>

<h2>Proposition:</h2>
<p>
	<?php	
	print $pInfo[TEXT];
	?>
</p>

<?php if ($dev) { ?>
	<h2>Implications involving this proposition:</h2>
	<?php
	$_is = mysql_fetch_row(mysql_query("SELECT implications FROM propositions WHERE p = $id"));
	$is = explode(',', $_is[0]);
	$anum = 1;
	foreach ($is as $i) {
		show_implication($i);
		/*$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i");
		if ($_ps) {
			$ps = mysql_fetch_row($_ps);
			$as[$anum]['c']['p'] = array_pop($ps);
			$pnum = 1;
			for ($i = $pnum - 2; $i > 0; $i--) {
				$cid = $as[$anum][c][p];
				$_text = mysql_query("SELECT text FROM propositions WHERE p = $cid");
				if ($_text) {
					$text = mysql_fetch_row($_text);
					print "<p>You would be committed to accepting: $text[0]</p>";
				} else {
					print "<p>You would be committed to accepting $cid</p>";
				}
				print "<p>If you accepted:</p>";
				$_text = mysql_query("SELECT text FROM propositions WHERE p =".$as[$anum][$i][p]);
				if ($_text) {
					$text = mysql_fetch_row($_text);
					print "<p>$text[0]</p>";
				}
			}
		} 
		*/
	}
}
?>

<div>
<?php
// USER'S STATUS
	if ($pInfo[IS_DEFINITIONAL]) {
		print "This is a definitional truth";
	} else if ($pInfo[IS_LOGICAL]) {
		print "This is a logical truth";
	} else {
		$_a = mysql_query("SELECT b, c FROM attitudes WHERE u = $u AND p = $id");
	}
	if ($_a) { ?>
		<h2>Your status:</h2>
		<?php
		$a = mysql_fetch_row($_a);
		if ($a[BELIEF] == ACCEPT) {
			print "<p><em>You accept this</em></p>";
		} else if ($a[BELIEF] == REJECT) {
			print "<p><em>You reject this</em></p>";
		}
		if ($a[COMMITMENT] == ACCEPT) {
			print "<p><em>You are committed to this</em></p>";
		} else if ($a[COMMITMENT] == REJECT) {
			print "<p><em>You committed to the falsity of this</em></p>";
		}
	}
?>
</div>

<?php
// CONTEXT
	if ($contextID = $pInfo[CONTEXT_ID]) { ?>
		<h2>Context for this propositon:</h2>
		<p>
			<?php
			$context_text = mysql_fetch_row(mysql_query("SELECT text FROM arg_text WHERE id = $contextID"));
			print $context_text[0];
			?>
		</p>
		<?php
	} 
?>



<h2>What this proposition commits you to, and would commit you too if you accepted other propositions:</h2>
<!-- Should only be shown if we find something below -->

<?php
	$_is = mysql_fetch_row(mysql_query("SELECT implications FROM propositions WHERE p = $id"));
	$is = explode(',', $_is[0]);
	$anum = 1;
	foreach ($is as $i) {
		$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i");
		if ($_ps) {
			$ps = mysql_fetch_row($_ps);
			$as[$anum]['c']['p'] = array_pop($ps);
			$pnum = 1;
			foreach ($ps as $p) {
				if ($p) {
				//	if ($p != $p) { // induces bug below where we run through $as[$anum][$i] and find a missing $i
/*	BUG TESTING
						print '<pre>';
						print_r($as);
						print '</pre>';
						print "\$as[$anum][$pnum]['p'] = $p;";
						$as[1][1]['p'] = 51;
						$as[$anum][$pnum]['p'] = $p;
*/
						$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $p AND u=$u");
						$pa = mysql_fetch_row($_pa);
						if ($pa[BELIEF] == ACCEPT || $pa[COMMITMENT] == ACCEPT) {
							$as[$anum][$pnum]['b'] = ACCEPT;
						} else {
							$as[$anum][$pnum]['b'] = REJECT;
						}
				//	}
					$pnum++;
				}
			}
			if ($a[BELIEF] == ACCEPT) {
				$committed = TRUE;
				for ($i = $pnum - 2; $i > 0; $i--) {
					if ($as[$anum][$i][b] != ACCEPT && $as[$anum][$i][b] != COMMITTED) {
						if ($committed) {
							$cid = $as[$anum][c][p];
							$_text = mysql_query("SELECT text FROM propositions WHERE p = $cid");
							if ($_text) {
								$text = mysql_fetch_row($_text);
								print "<p>You would be committed to accepting: $text[0]</p>";
							} else {
								print "<p>You would be committed to accepting $cid</p>";
							}
							print "<p>If you accepted:</p>";
						}
						$committed = FALSE;
						$_text = mysql_query("SELECT text FROM propositions WHERE p =".$as[$anum][$i][p]);
						if ($_text) {
							$text = mysql_fetch_row($_text);
							print "<p>$text[0]</p>";
						}
						// Has this covered case where 2 extra acceptances would be nec?
						// Ideally add commitments though arguments - register these in an extra columns in either props or subarguments
					}
				}
				if ($committed) {
					$cid = $as[$anum][c][p];
					$_conca = mysql_query("SELECT b, c FROM attitudes WHERE p = $cid AND u=$u");
					$conca = mysql_fetch_row($_conca);
					if ($conca) {
						$as[$anum][c][b] = $conca[0];
					} else {
						$as[$anum][c][b] = 0;
					}
					if ($as[$anum][c][b] != ACCEPT && $as[$anum][c][b] != COMMITTED) {
						// Tell the user they're committed
						$_text = mysql_query("SELECT text FROM propositions WHERE p = $cid");
						if ($_text) {
							$text = mysql_fetch_row($_text); 
							print "<p><b>You are committed to accepting:</b> $text[0]</p>";
						}
					}
				}
			}
//			$as[$anum][$pnum][followed] = $committed;
			
		}
	}
?>
</div>
</body>
</html>