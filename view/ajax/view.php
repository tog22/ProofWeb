<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>
		<?php
		require '../../functions/functions.php';
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
				$view[WHAT] = 'argument';
				break;
			case 'proposition':
				$view[WHAT] = 'proposition';
				break;
			default:
				print 'ERROR: Invalid type';
				break;
		}
		if ($id == 'all') {
			$view[WHICH] = 'all';
		} else if (is_numeric($id)) {
			$view[WHICH] = $id;
			if ($type == 'implication') {
				print "Implication $id | ProofWeb";
			} else if ($type == 'argument') {
				if ($_arg_name = mysql_query("SELECT name FROM arguments WHERE id = $id")) {
					$arg_name =  mysql_fetch_row($_arg_name);
					print $arg_name[0] . ' | ProofWeb';
				} else {
					print 'Invalid argument ID';
				}
			} else if ($type == 'proposition') {
				print db_fetch("SELECT text FROM propositions WHERE p = $id"); ?> | ProofWeb<?php
			}
		} else {
			print 'ERROR: Invalid ID';
		}
		if ($_GET['dev']) {
			$devMode = TRUE;
		}
		?>
	</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />
	<script type="text/javascript" src="libraries/core.js"></script>
	<script type="text/javascript" src="libraries/ajax.js"></script>
	<script type="text/javascript" src="libraries/toglibrary.js"></script>
	<script type="text/javascript" src="libraries/formdata2querystring.js"></script>
	<?php if ($type == 'proposition') { ?>
	<script type="text/javascript">
		var propsShown = [];
		var ActiveLinks = {
			init: function() {
				var buttons = Core.getElementsByClass('button');
				for (i = 0; i < buttons.length; i++) {
					Core.addEventListener(buttons[i].firstChild, 'click', ActiveLinks.submitListener);
					propsShown[i] = [buttons[i].firstChild.className, buttons[i].className, buttons[i]];
				}
			},
			
			submitListener: function(event) {
				Core.preventDefault(event);
				// Change the icon
				<?php if ($view[WHAT] == 'argument') : ?>
					switch (this.title) {
						case 'Accept':
							var toSet = 1;
							/* set cross to off - move to handler, based on response
							var relatedButtons = Core.getElementsByClass(this.className);
							for (k = 0; k < relatedButtons.length; k++) {
								if (relatedButtons[k].parentNode.className == 'cross-on button') {
									relatedButtons[k].parentNode.className = 'cross-off button';
								}
							} */
							break;
						case 'Reject':
							var toSet = 1;
							/* set tick to off - move to handler, based on response
							var relatedButtons = Core.getElementsByClass(this.className);
							for (k = 0; k < relatedButtons.length; k++) {
								if (relatedButtons[k].parentNode.className == 'tick-on button') {
									relatedButtons[k].parentNode.className = 'tick-off button';
								}
							} */
							break;
						case 'Take this back':
							var toSet = 'NULL';
							if (this.parentNode.className == 'tick-on button') {
								this.parentNode.className = 'tick-off button';
								this.title = 'Accept';
								this.href = '/props/functions/php/set-attitude.php?p=' + this.className + '&amp;a=1';
							} else if (this.parentNode.className == 'cross-on button') {
								this.parentNode.className = 'cross-off button';
								this.title = 'Reject';
								this.href = '/props/functions/php/set-attitude.php?p=' + this.className + '&amp;a=2';
							}
							
							break;
					}
				<?php endif; ?>
				// Tell the database
				var ajax = new Ajax();
				if (this.classList[1] == 'committed') {
					var c = 1;
				} else {
					var c = 'NULL';
				}
				var request = '../../functions/ajax/setattitude.php?p='+this.classList[0]+'&b='+toSet+'&c='+c;
				ajax.doGet(request, ActiveLinks.handler,'xml');
			},
			
			handler: function(response) {
				tog.showMessages(response);
				var attitudeChanges = response.getElementsByTagName('attitudeChange');
				for (i = 0; i < attitudeChanges.length; i++) {
					// TO FINISH... Run through attitudeChange elements to match against props below and if nec. update buttons
					var p = attitudeChanges[i].getElementsByTagName('p')[0].firstChild.wholeText;
					var belief = attitudeChanges[i].getElementsByTagName('belief')[0].firstChild.wholeText;
					var c = attitudeChanges[i].getElementsByTagName('c')[0].firstChild.wholeText;
					for (j = 0; j < propsShown.length; j++) {
						if (p == propsShown[j][0]) {
							if (belief == 1) {
								if (propsShown[j][2].classList[0] == 'tick-off') {
									propsShown[j][2].className = 'tick-on button';
								} else if (propsShown[j][2].classList[0] == 'cross-off') {
									propsShown[j][2].className = 'cross-on button';
								}
								propsShown[j][2].title = 'Take this back';
								propsShown[j][2].firstChild.href = '/props/functions/php/set-attitude.php?p=' + propsShown[j][0] + '&amp;a=NULL';
							} else if (belief == 2) {
								if (propsShown[j][2].classList[0] == 'tick-on') {
									propsShown[j][2].className = 'tick-off button';
									propsShown[j][2].title = 'Accept';
								} else if (propsShown[j][2].classList[0] == 'cross-on') {
									propsShown[j][2].className = 'cross-off button';
									propsShown[j][2].title = 'Reject';
								}
								propsShown[j][2].firstChild.href = '/props/functions/php/set-attitude.php?p=' + propsShown[j][0] + '&amp;a=1';
							} else if (c == 1) {
								if (propsShown[j][0]%2) {
									propsShown[j][2].className = 'cross-c button';
								} else {
									propsShown[j][2].className = 'tick-c button';
								}
							} else if (c == 2 || c == 'NULL') {
								if (propsShown[j][2].className == 'tick-c button') {
									propsShown[j][2].className = 'tick-off button';
								} else if (propsShown[j][2].className == 'cross-c button') {
									propsShown[j][2].className = 'cross-off button';
								}
							}
						}
					}
				}
			}
		}; 
		
		Core.start(ActiveLinks);
	</script>
	<?php } ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />
</head>
<body>
<!-- get html for arg by calling view.php & insert here - can we grap the paramaeters of this page using JavaScript rather than PHP? (is there any reason to bother besides as a learning exercise, given that seeing a new argument can acceptably require a page refresh? I've got PHP parsing power to burn -->
	<div id="main">
		<p><em>Remember to disable your cache!</em></p>
		<?php
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
		} else if ($view[WHAT] == 'proposition') {
			?>
			<h1><?php print db_fetch("SELECT text FROM propositions WHERE p = $id"); ?></h1>
			<p class="center">
				<?php
				$attitude = db_fetch("SELECT b, c FROM attitudes WHERE p = $id AND u = $u", CAN_BE_EMPTY);
				if (!$attitude) {
					$attitude = array();
					$attitude[0] = NEUTRAL;
					$attitude[1] = NEUTRAL;
				}
				print_ajax_buttons($id, $attitude[0], $attitude[1]);
				?>
				</div>
			</p>
			<?php
			$HAS_ARGUMENTS = FALSE; //set
			if ($HAS_ARGUMENTS) { ?>
				<h2>Arguments for and against this proposition</h2>
				<?php
			} 
			$comments = db_query("SELECT text, official FROM prop_comments WHERE p = $id");
			if ($comments) { ?>
				<a name="comments" id="comments"></a>
				<h2>Comments</h2>
				<?php
				while ($comment = mysql_fetch_row($comments)) {
					if ($comment[1]) { ?>
						<div class="official-comment">ProofWeb commentary</div>
						<?php
					} ?>
					<div class="comment-text">
						<?php print $comment[0]; ?>
					</div>
					<?php
				}
			}
			?>
			
			
			
			<?php
		}
		?>
	</div>
</body>
</html>
