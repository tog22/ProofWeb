<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
if (isset($_GET['proposition'])) {
	$proposition = "'".addslashes($_GET['proposition'])."'";
	if ($_GET['negation']) {
		$negation = "'".addslashes($_GET['negation'])."'";
	} else {
		$lcprop = $_GET['proposition']; // should be lcfirst($_GET['proposition']) - though doesn't handle cases where prop begins with, e.g., a quote mark or [ sign
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