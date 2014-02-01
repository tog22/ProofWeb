<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />
    <script type="text/javascript" src="core.js"></script>
	<script type="text/javascript">
		var button =
		{
			init: function() {
				var buttonArray = Core.getElementsByClass("button");
				for (var i = 0; i < buttonArray.length; i++) {
					buttonArray[i].onclick = button.clickHandler;
				}
			}
			
			clickHandler: function() {
				alert("Don't believe everything you read on Wikipedia!");
			}
		}
		
		Core.start(button);
	</script>
</head>
<body>
<?php
	require 'functions.php';
?>
<div id="page">

  <div id="header">
	<h1>proofs.PhilosoFiles</h1>
  </div>

<div id="main">  
  <div id="col1">
  <div>
	<div id="app-explanation">
		<p>proofs.PhilosoFiles is an interactive way of exploring hundreds of arguments from the history of philosophy. It knows the <abbr class="gloss" title="">logical relations<a class="glossary" href="#">?</a></abbr> between <abbr class="gloss" title="A proposition is claim - something that can be true or false">propositions<a class="glossary" href="#">?</a></abbr>, and so can show you what those you accept imply - or what might imply those you have yet to accept.</p>
		<p>Below you can explore from either angle, seeing famous arguments <em>for</em> the existence of God or checking whether there is a proof <em>from</em> propositions you already accept to the counter-intuitive 'sceptical' position that you cannot know anything about the external world.</p>
	</div>
	
	<p class="navlabel"><a href="browse.php">Browse all arguments</a>, or see arguments for the following propositions:</p>
	<p><a href="argument.php?id=1">Something (often interpreted as God) is uncaused and exists necessarily</a></p>
	<p><a href="argument.php?id=2">Equal welfare is not intrinsically valuable</a></p>
</div>
  </div>
  
  <div id="col2">	
	<div id="featured-argument">
		<h2>Featured argument:</h2>
		<?php
		$id = 1;
		$_is = mysql_fetch_row(mysql_query("SELECT subargs FROM arguments WHERE id = $id"));
		$is = explode(',', $_is[0]);
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
  </div>
	

</div>
</div>
</body>
</html>