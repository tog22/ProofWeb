<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
if ($_GET['i1']) {
	$subargs = "'".$_GET['i1'];
	if ($_GET['i2']) {
		$subargs .= ',';
		$subargs .= $_GET['i2'];
	}
	if ($_GET['i3']) {
		$subargs .= ',';
		$subargs .= $_GET['i3'];
	}
	if ($_GET['i4']) {
		$subargs .= ',';
		$subargs .= $_GET['i4'];
	}
	if ($_GET['i4']) {
		$subargs .= ',';
		$subargs .= $_GET['i4'];
	}
	if ($_GET['i5']) {
		$subargs .= ',';
		$subargs .= $_GET['i5'];
	}
	if ($_GET['i6']) {
		$subargs .= ',';
		$subargs .= $_GET['i6'];
	}
	if ($_GET['i7']) {
		$subargs .= ',';
		$subargs .= $_GET['i7'];
	}
	if ($_GET['i8']) {
		$subargs .= ',';
		$subargs .= $_GET['i8'];
	}
	if ($_GET['i9']) {
		$subargs .= ',';
		$subargs .= $_GET['i9'];
	}
	if ($_GET['i10']) {
		$subargs .= ',';
		$subargs .= $_GET['i10'];
	}
	$subargs .= "'";
	$name = "'".addslashes($_GET['name'])."'";
	$description = "'".addslashes($_GET['description'])."'";
	$source = str_replace('"', 'SHOULDBEQUOTE', $_GET['source']);
	$source = "'".addslashes($source)."'";
	$source_author = "'".addslashes($_GET['source_author'])."'";
	// SQL and reporting
	$newID = mysql_fetch_row(mysql_query("SELECT MAX(id) + 1 FROM arguments"));
	if (!mysql_query("INSERT INTO arguments (subargs,name,description,source,source_author) VALUES ($subargs,$name,$description,$source,$source_author)")) { ?>
		<db-error>
			<query><?php print "INSERT INTO arguments (subargs,name,description,source,source_author) VALUES ($subargs,$name,$description,$source,$source_author)"; ?></query>
			<error>
				<? print mysql_error(); ?>
			</error>
		</db-error>
	<? } else { ?>
		<message>Added new argument (id <? print $newID[0]; ?>)</message>
	<? }
	/* OLD SQL and reporting:
	if (!mysql_query("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)")) { ?>
		<db-error>
			<query><?php print "INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)"; ?></query>
			<error>
				<? print mysql_error(); ?>
			</error>
		</db-error>
		</proveitResponse>
		<? return;	
	}
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
	<? } */
}
?>
</proveitResponse>