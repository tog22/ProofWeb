<?php
	require '../../model/functions.php';
	set_attitude($_GET['p'], $_GET['a']);
	header('Location: '.$_SERVER['HTTP_REFERER']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="styles.css" />
</head>
<body>
	<p>Attitude set - now returning you to where you were before...</p>
	<script type="text/javascript">
		//hhistory.go(-1);
	</script>
</body>
</html>