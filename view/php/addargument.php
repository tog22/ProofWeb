<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Add argument</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />
</head>
<body>
</body>
</html>
<?php
	require '../../functions/functions.php';
?>
<h1>Add argument</h1>

<form id="add-arg" class="add" action="addargument.php" method="post">
  <div>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name"/>
  </div>
  <div>
    <label for="i1">Implication 1:</label>
    <input type="text" class="number" name="i1" id="i1"/>
  </div>
  <div>
    <label for="i2">Implication 2:</label>
    <input type="text" class="number" name="i2" id="i2"/>
  </div>
  <div>
    <label for="i3">Implication 3:</label>
    <input type="text" class="number" name="i3" id="i3"/>
  </div>
  <div>
    <label for="i4">Implication 4:</label>
    <input type="text" class="number" name="i4" id="i4"/>
  </div>
  <div>
    <label for="i5">Implication 5:</label>
    <input type="text" class="number" name="i5" id="i5"/>
  </div>
  <div>
    <label for="i6">Implication 6:</label>
    <input type="text" class="number" name="i6" id="i6"/>
  </div>
  <div>
    <label for="i7">Implication 7:</label>
    <input type="text" class="number" name="i7" id="i7"/>
  </div>
  <div>
    <label for="i8">Implication 8:</label>
    <input type="text" class="number" name="i8" id="i8"/>
  </div>
  <div>
    <label for="i9">Implication 9:</label>
    <input type="text" class="number" name="i9" id="i9"/>
  </div>
  <div>
    <label for="i10">Implication 10:</label>
    <input type="text" class="number" name="i10" id="i10"/>
  </div>
  <div>
    <label for="contextpt">Pre-text:</label>
    <input type="text" name="contextpt" id="contextpt"/>
  </div>
  <div>
    <label for="source">Source:</label>
    <input type="text" name="source" id="source"/>
  </div>
  <div>
    <label for="source_author">Source author:</label>
    <input type="text" name="source_author" id="source_author"/>
  </div>
  <div>
    <input type="submit" name="submit" id="submit" 
        value="Add"/> 
  </div>
</form>

<?php
if ($_POST['i1']) {
	$subargs = "'".$_POST['i1'];
	if ($_POST['i2']) {
		$subargs .= ',';
		$subargs .= $_POST['i2'];
	}
	if ($_POST['i3']) {
		$subargs .= ',';
		$subargs .= $_POST['i3'];
	}
	if ($_POST['i4']) {
		$subargs .= ',';
		$subargs .= $_POST['i4'];
	}
	if ($_POST['i4']) {
		$subargs .= ',';
		$subargs .= $_POST['i4'];
	}
	if ($_POST['i5']) {
		$subargs .= ',';
		$subargs .= $_POST['i5'];
	}
	if ($_POST['i6']) {
		$subargs .= ',';
		$subargs .= $_POST['i6'];
	}
	if ($_POST['i7']) {
		$subargs .= ',';
		$subargs .= $_POST['i7'];
	}
	if ($_POST['i8']) {
		$subargs .= ',';
		$subargs .= $_POST['i8'];
	}
	if ($_POST['i9']) {
		$subargs .= ',';
		$subargs .= $_POST['i9'];
	}
	if ($_POST['i10']) {
		$subargs .= ',';
		$subargs .= $_POST['i10'];
	}
	$subargs .= "'";
	$name = "'".addslashes($_POST['name'])."'";
	$description = "'".addslashes($_POST['description'])."'";
	$source = "'".addslashes($_POST['source'])."'";
	$source_author = "'".addslashes($_POST['source_author'])."'";
	$ck = mysql_query("INSERT INTO arguments (subargs,name,description,source,source_author) VALUES ($subargs,$name,$description,$source,$source_author)");
	// Success-reporting
	print ("INSERT INTO arguments (subargs,name,description,source,source_author) VALUES ($subargs,$name,$description,$source,$source_author)");
	
	if ($ck) {
		print "<p style='color:green; font-weight:bold;'>SUCCEEDED</p>";
	} else {
		print "<p style='color:red; font-weight:bold;'>FAILED</p>";
	}
}
?>