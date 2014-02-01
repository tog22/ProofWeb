<body>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Add implication</title>
	  <script type="text/javascript" src="ajax.js"></script>
	  <script type="text/javascript">
      var hand = function(str) {
        alert(str);
      }
      function onchangetest () {
      	
      }
      var ajax = new Ajax();
      //ajax.doGet('fakeserver.php', hand);
	  </script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="styles.css" />

</head>
<body>
<?php
	require '../functions.php';
?>
<h1>Add implication</h1>

<form action="addimplication.php" method="post">
  <div>
    <label for="p1">Premise 1:</label>
    <input type="text" name="p1" id="p1" onchange="onchangetest();"/>
  </div>
  <div>
    <label for="p2">Premise 2:</label>
    <input type="text" name="p2" id="p2"/>
  </div>
  <div>
    <label for="p3">Premise 3:</label>
    <input type="text" name="p3" id="p3"/>
  </div>
  <div>
    <label for="p4">Premise 4:</label>
    <input type="text" name="p4" id="p4"/>
  </div>
  <div>
    <label for="c">Conclusion:</label>
    <input type="text" name="c" id="c"/>
  </div>
  <div>
    <input type="submit" name="submit" id="submit" 
        value="Add"/> 
  </div>
</form>

<?php
if ($_POST['p1'] && $_POST['c']) {
	create_implication($_POST['c'],$_POST['p1'],$_POST['p2'],$_POST['p3'],$_POST['p4']);
	if (!$_POST['p2']) { // 1 premise version
		create_converse_implication($_POST['p1'],$_POST['c']);
	} else if (!$_POST['p3']) { // 2 premise versions
		create_converse_implication($_POST['p1'],$_POST['c'],$_POST['p2']);
		create_converse_implication($_POST['p2'],$_POST['c'],$_POST['p1']);
	} else if (!$_POST['p4']) { // 3 premise versions
		create_converse_implication($_POST['p1'],$_POST['c'],$_POST['p2'],$_POST['p3']);
		create_converse_implication($_POST['p2'],$_POST['c'],$_POST['p1'],$_POST['p3']);
		create_converse_implication($_POST['p3'],$_POST['c'],$_POST['p1'],$_POST['p2']);
	} else { // 4 premise versions
		create_converse_implication($_POST['p1'],$_POST['c'],$_POST['p2'],$_POST['p3'],$_POST['p4']);
		create_converse_implication($_POST['p2'],$_POST['c'],$_POST['p1'],$_POST['p3'],$_POST['p4']);
		create_converse_implication($_POST['p3'],$_POST['c'],$_POST['p1'],$_POST['p2'],$_POST['p4']);
		create_converse_implication($_POST['p4'],$_POST['c'],$_POST['p1'],$_POST['p2'],$_POST['p3'],$_POST['p4']);
	}
}
?>
</body>
</html>
