<?php
require '../functions.php';
if (isset($_GET['proposition'])) {
	$proposition = "'".addslashes($_GET['proposition'])."'";
	if ($_GET['negation']) {
		$negation = "'".addslashes($_GET['negation'])."'";
	} else {
		$lcprop = lcfirst($_GET['proposition']); // doesn't handle cases where prop begins with, e.g., a quote mark or [ sign
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
	// Make the queries and report on their failure or else redirect back to the form
	if (!mysql_query("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($proposition,$contextp,$contextpt,$definitional,$logical)")) {
		print "db-error//INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($proposition,$contextp,$contextpt,$definitional,$logical)//".mysql_error();
		return;
	}
	if (!mysql_query("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)")) {
		print "db-error//INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)//".mysql_error();
		return;	
	}
	// TO DO: REDRIECT BACK TO THE FORM
	print "TO DO: REDRIECT BACK TO THE FORM";
}
?>