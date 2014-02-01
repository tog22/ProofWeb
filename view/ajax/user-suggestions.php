<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Arguments for and against | ProofWeb</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />
	<script type="text/javascript" src="libraries/core.js"></script>
	<script type="text/javascript" src="libraries/ajax.js"></script>
	<script type="text/javascript" src="libraries/toglibrary.js"></script>
	<script type="text/javascript" src="libraries/formdata2querystring.js"></script>
	<script type="text/javascript">
	</script>
	<?php
		require '../../model/functions.php';
	?>
</head>
<body>
	<div id="main">
		<h1>User suggestions</h1>
		<p><em>Remember to disable your cache!</em></p>
		
		<?php
		if ($close) {
			?>
			<h2>Propositions you're close to accepting</h2>
			<?php
		}
		?>
		<?php
		if ($contradictions) {
			?>
			<h2>You're contradicting yourself</h2>
			<?php
		}
		?>
	</div>
</body>
</html>
