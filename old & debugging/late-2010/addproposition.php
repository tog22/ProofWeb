<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Add proposition</title>
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
<h1>Add proposition</h1>

<form action="addproposition.php" method="post" class="add">
  <div>
    <label for="proposition">Proposition:</label>
    <input type="text" name="proposition" id="proposition"/>
  </div>
  <div>
    <label for="negation">Negation (optional):</label>
    <input type="text" name="negation" id="negation"/>
  </div
  <div>
    <label for="contextp">Context proposition (optional):</label>
    <input type="text" name="contextp" id="contextp"/>
  </div>
  <div>
    <label for="contextpt">Context pre-text (optional):</label>
    <input type="text" name="contextpt" id="contextpt"/>
  </div>
  <div>
    <label for="definitional">Definitional?</label>
    <input type="checkbox" name="definitional" id="definitional"/>
  </div>
  <div>
    <label for="logical">Logical truth?</label>
    <input type="checkbox" name="logical" id="logical"/>
  </div>
  <div>
    <input type="submit" name="submit" id="submit" 
        value="Add"/> 
  </div>
</form>

<?php
if (isset($_POST['proposition'])) {
	$proposition = "'".addslashes($_POST['proposition'])."'";
	if ($_POST['negation']) {
		$negation = "'".addslashes($_POST['negation'])."'";
	} else {
		$lcprop = lcfirst($proposition); // doesn't handle cases where prop begins with, e.g., a quote mark or [ sign
		$negation = "It is not the case that ".$lcprop;
	}
	if (!($contextp = $_POST['contextp'])) {$contextp = 'NULL';}
	if (!($contextpt = $_POST['contextpt'])) {$contextpt = 'NULL';}
	if (isset($_POST['definitional'])) {
		$definitional = TRUTH;
		$negation_definitional = FALSITY;
	} else {
		$definitional = 'NULL';
		$negation_definitional = 'NULL';
	}
	if (isset($_POST['logical'])) {
		$logical = TRUTH;
		$negation_logical = FALSITY;
	} else {
		$logical = 'NULL';
		$negation_logical = 'NULL';
	}
	$ck1 = mysql_query("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($proposition,$contextp,$contextpt,$definitional,$logical)");
	//print ("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($proposition,$contextp,$contextpt,$definitional,$logical)");($negation) {
	$ck2 = mysql_query("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)");
	//print ("INSERT INTO propositions (text,contextp,contextpt,definitional,logical) VALUES ($negation,$contextp,$contextpt,$negation_definitional,$negation_logical)");
	
	if ($ck1 && $ck2) {
		print "<p style='color:green; font-weight:bold;'>SUCCEEDED</p>";
	} else {
		print "<p style='color:red; font-weight:bold;'>FAILED</p>";
	}
}
?>