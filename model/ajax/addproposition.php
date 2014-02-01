<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
if (isset($_GET['proposition'])) {
	$proposition = "'".addslashes($_GET['proposition'])."'";
	if ($_GET['negation']) {
		$negation = "'".addslashes($_GET['negation'])."'";
	} else {
		$lcprop = $_GET['proposition'];
		$i = 0;
		$length = strlen($lcprop);
		/* for some reason the first { brace in this is unexpected - though this could be a false error
		while (!$FIRST_LETTER_REPLACED) {
			if (ctype_alpha($lcprop[$i]) {
				$lcprop[$i] = strtolower($lcprop[$i]);
				$FIRST_LETTER_REPLACED = TRUE;
			}
			$i++;
			if ($i >= $length) {
				$FIRST_LETTER_REPLACED = TRUE;
			}
		}*/
		$negation = "'It is not the case that ".$lcprop."'";
	}
	if (!($contextp = $_GET['contextp'])) {$contextp = 'NULL';}
	if (!($contextpt = $_GET['contextpt'])) {$contextpt = 'NULL';}
	if (isset($_GET['definitional'])) {
		$definitional = TRUTH;
		$negation_definitional = FALSITY;
	} else {
		$definitional = 'NULL';
		$negation_definitional = 'NULL';
	}
	if (isset($_GET['logical'])) {
		$logical = TRUTH;
		$negation_logical = FALSITY;
	} else {
		$logical = 'NULL';
		$negation_logical = 'NULL';
	}
	// Make the queries and report on their success
	if (!mysql_query("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($proposition,$contextp,$contextpt,$definitional,$logical)")) {
		print "db-error//INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($proposition,$contextp,$contextpt,$definitional,$logical)//".mysql_error();
		return;
	}
	if (!mysql_query("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)")) { ?>
		<db-error>
			<query><?php print "INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)"; ?></query>
			<error>
				<? print mysql_error(); ?>
			</error>
		</db-error>
	<? } else {
		if ($_newID = mysql_query("SELECT MAX(p) - 1 FROM propositions")) {
			$newID = mysql_fetch_row($_newID); ?>
			<message>Adding proposition <? print $newID[0]; ?> succeeded</message>
		<? } else { ?>
			<message>Insert didn't return error, but couldn't get new ID...</message>
			<db-error>
				<query>SELECT MAX(id) - 1 FROM propositions;</query>
				<error>
					<? print mysql_error(); ?>
				</error>
			</db-error>
		<? }
	}
}
?>
</proveitResponse>