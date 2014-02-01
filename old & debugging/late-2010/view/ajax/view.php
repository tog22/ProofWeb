<body>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>ProveIt</title>
	<script type="text/javascript" src="libraries/core.js"></script>
	<script type="text/javascript" src="libraries/ajax.js"></script>
	<script type="text/javascript" src="libraries/toglibrary.js"></script>
	<script type="text/javascript" src="libraries/formdata2querystring.js"></script>
	<script type="text/javascript">
		var ActiveLinks = {
			init: function() {
				var buttons = Core.getElementsByClass('button');
				for (i = 0; i < buttons.length; i++) {
					Core.addEventListener(buttons[i].firstChild, 'click', ActiveLinks.submitListener);
				}
			},
			submitListener: function(event) {
				Core.preventDefault(event);
				var ajax = new Ajax();
				<?php
				$type = mysql_real_escape_string($_GET['type']);
				$id = mysql_real_escape_string($_GET['id']);
				$view = array();
				define('WHAT', 0);
				define('WHICH', 1);
				switch ($type) {
					case 'implication':
						$view[WHAT] = 'implication';
						break;
					case 'argument':
						$view[WHAT] = 'argument'; ?>
						switch (this.title) {
							case 'Accept':
								var toSet = 1;
								break;
							case 'Reject':
								var toSet = 2;
								break;
							case 'Take this back':
								var toSet = 3;
								break;
						}
						if (this.classList[1] == 'committed') {
							var c = 1;
						} else {
							var c = 0;
						}
						ajax.doGet('http://localhost/proveit/model/ajax/setattitude.php?p='+this.classList[0]+'&b='+toSet+'&c='+c, ActiveLinks.handler,'xml');
						<?php break;
				}
				if ($id == 'all') {
					$view[WHICH] = 'all';
				} else {
					$view[WHICH] = $id;
				}
				if ($_GET['dev']) {
					$devMode = TRUE;
				}
				?>
			},
			handler: function(response) {
				tog.showMessages(response);
				// Run through attitudeChange elements to match against props below and if nec. update buttons
				var attitudeChanges = response.getElementsByTagName('attitudeChange');
				for (i = 0; i < attitudeChanges.length; i++) {
					var test = attitudeChanges[i].children[0].wholeText;
				}
				var mainDiv = document.getElementById('main');
				if (message) {
					tog.addElement('div','message',message.firstChild.wholeText,mainDiv);
				}
			}
		}; 
		
		Core.start(ActiveLinks);
	</script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />
</head>
<body>
<!-- get html for arg by calling view.php & insert here - can we grap the paramaeters of this page using JavaScript rather than PHP? (is there any reason to bother besides as a learning exercise, given that seeing a new argument can acceptably require a page refresh? I've got PHP parsing power to burn -->
	<div id="main">
		<?php
		require '../../model/functions.php';
		if ($view[WHAT] == 'argument') {
			if ($view[WHICH] != 'all') {
				show_argument($id, TRUE);
			} else if ($view[WHICH] == 'all') {
				$maxid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM arguments"));
				for ($i = 1; $i <= $maxid; $i++) {
					show_argument($i, TRUE);
				}
			}
		} else if ($view[WHAT] == 'implication') {
			if ($view[WHICH] != 'all') {
				show_implication($id);
			} else if ($view[WHICH] == 'all') {
				$maxid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM implications"));
				for ($i = 1; $i <= $maxid; $i++) {
					show_implication($i);
				}
			}
		}
		?>
	</div>
</body>
</html>
