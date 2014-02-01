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

  <div id="header">
	<h1>proofs.PhilosoFiles</h1>
  </div>

<div id="main">  
  <div id="col1">
  <div>
	<div id="app-explanation">
		<p>proofs.PhilosoFiles is an interactive way of exploring hundreds of arguments from the history of philosophy. It knows the <abbr class="gloss" title="">logical relations<a class="glossary" href="#">?</a></abbr> between <abbr class="gloss" title="A proposition is a claim - something that can be true or false">propositions<a class="glossary" href="#">?</a></abbr>, and so can show you what those you accept imply - or what might imply those you have yet to accept.</p>
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
		show_argument(1);
		?>
  	</div>
  </div>
	

</div>
</div>
</body>
</html>