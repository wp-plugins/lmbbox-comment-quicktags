<?php
/*
Plugin Name: LMB^Box Comment Quicktags
Plugin URI: http://aboutme.lmbbox.com/lmbbox-plugins/lmbbox-comment-quicktags/
Description: Inserts a quicktag toolbar in the blog comment form.
Version: 1.7.1
Author: Thomas Montague
Author URI: http://aboutme.lmbbox.com
*/ 
/*
LMB^Box Comment Quicktags -> Was WP Comment Quicktags Plus!
version 1.7.1, 2005-07-17
By Thomas Montague

This Plugin is modified code from Owen Winkler's Comment Quicktags Plugin
****** Plugin Info ********
Plugin Name: Comment Quicktags
Plugin URI: http://www.asymptomatic.net/wp-hacks
Description: Inserts a quicktag toolbar on the blog comment form.
Version: 1.0
Author: Owen Winkler
Author URI: http://www.asymptomatic.net


LMB^Box Comment Quicktags is a plugin that puts a Quicktag Toolbar, just like the admin's 
Quicktags Toolbar, right above the comments text form area. You can use the bar to add 
strong/bold, em/italic, code, blockquote, and links tags by default. You can add more or 
remove some if you do not want them there. This allows general users/guests to be able to 
post comments with advanced editing features.


== Installation ==

1. Copy the file lmbbox-comment-quicktags.php into your wp-content/plugins directory
2. Activate this plugin in the WordPress Admin Panel
3. In your comments.php theme file add 
	"<?php if(function_exists(lmbbox_comment_quicktags)) { lmbbox_comment_quicktags(); } ?>" 
	right above the <textarea name="comment" id="comment" ....></textarea>. I put mine just 
	above my commented out allowed_tags() call, example below.

	-- Example --
		<p>
			<?php if(function_exists(lmbbox_comment_quicktags)) { lmbbox_comment_quicktags(); } ?>
			<!-- <small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small> //-->
			<textarea name="comment" id="comment" cols="100%" rows="12" tabindex="4" onkeyup="ReloadTextDiv();"></textarea>
		</p>
	-- End of Example --

3.1 You need to make sure that "<?php do_action('comment_form', $post->ID); ?>" is just after the </div> and just before 
	the </form> lines at the bottom of the comments.php file in your theme's folder.

3.2 Open up your comments.php file and find the same area as you did in step 3 above. Now if you notice in my example 
	above, the <textarea></textarea> line has 'name="comment" id="comment"' settings. Now you need to make sure that both 
	the name and id are "comment" like above (the default theme is like this).


#### Styling the Toolbar ####
4. The code added at the lmbbox_comments_quicktags() function call has some CSS styling tags 
	in place. Below is what I have for my style settings, which mimic the admin's quicktags 
	look. There is a div with a id of comment_quicktags that wraps the whole of the code. 
	Then in the quicktags script, a link (<a href="http://wordpress.org/docs/reference/post/#quicktags" title="Help with quicktags">Quicktags</a>:) 
	is displayed followed by a div with an id of ed_comment_toolbar. The id of the div can 
	be used by anybody who may want to add more buttons after the hard coded buttons 
	(eg. My plugin -> LMB^Box Smileys). All of the buttons are inputs with 
	a class of ed_button, and all of the buttons have ids of ed_%Name_of_button%. The build 
	in button ids are: ed_strong, ed_em, ed_pre, ed_block, and ed_link. 

---------------------- Comment Quicktags ---------------------------
#comment_quicktags {
	text-align: left;
	margin-left: 1%;
}
#comment_quicktags #ed_comment_toolbar {
	display: inline;
}
#comment_quicktags input.ed_button {
	background: #F4F4F4;
	border: 1px solid #D6D3CE;
	color: #000000;
	font-family: Georgia, "Times New Roman", Times, serif;
	margin: 1px;
	width: auto;
}
#comment_quicktags input:focus.ed_button {
	background: #FFFFFF;
	border: 1px solid #686868;
}
#comment_quicktags #ed_strong {
	font-weight: bold;
}
#comment_quicktags #ed_em {
	font-style: italic;
}


#### Edit the Quicktag Buttons ####
5. If you wish to change/edit the Quicktag Buttons that are displayed, all you need to do is 
	go to the LMB^Box Comment Quicktags Options Page. On the page you will be able to set the 
	Minimum Level to Change LMB^Box Comment Quicktags Settings, if to display LMB^Box Comment 
	Quicktags Toolbar in Comment Form, and add/edit/manage/remove the Quicktags Toolbar Buttons. 
	In the default buttons, there are two that use special builting functions. Do NOT add any 
	settings to these buttons! They are ed_img and ed_link. To add a new button, put in a Button ID 
	and a Button Lable at the least. To make the button work, fill in Open and Close Tags (these 
	are html code tags that will be used to style the output of the text between the tags). You 
	can also add a Keyboard Shortcut key (I don't know how to use this though). The only time you 
	need to check "Button Doesn't Need Close Tag" is if the Open Tag is all you need to show the 
	html code tag that you want (on ed_img button, leave this box checked!). Then to display the 
	Button, check the "Display Button" checkbox. If you want to remove a button, either just check 
	the "Remove Button" checkbox or remove the Button ID and Button Lable. To change the order of 
	display for the buttons, use the first column Button Display Order to move each button to where 
	you want it to be by moving them up or down the list.


#### Uninstallation ####
5. If you want to uninstall LMB^Box Comment Quicktags, just deactivate the plugin in your Plugins Page. 
	When you click on deactivate, you will be prompt to if to remove LMB^Box Comment Quicktags Settings. 
	Click Ok/Yes to remove settings or Cancel/No to keep settings.


== End of Installation ==
*/

/*
ToDo:
	Add Options page
	Add support for custom buttons and button order, edit button, no display of button!
*/

if (defined('ABSPATH')) {
	// Setting removal code on deactivation of plugin!
	if (strstr($_SERVER['PHP_SELF'], 'plugins.php') && $_GET['action'] == 'deactivate' && $_GET['plugin'] == 'lmbbox-comment-quicktags.php') {
		switch ($_GET['comment_quicktags_remove']) {
			case 'options':
				delete_option('lmbbox_comment_quicktags_options');
				break;
			case 'none':
				break;
			default:
				echo 	'
						<script language="JavaScript" type="text/javascript">
						<!--
							var remove_options = confirm(\'Do you wish to remove your LMB^Box Comment Quicktags Option Settings?\');
							if (remove_options) {
								window.location = \'plugins.php?action=deactivate&plugin=lmbbox-comment-quicktags.php&comment_quicktags_remove=options\';
							} else {
								window.location = \'plugins.php?action=deactivate&plugin=lmbbox-comment-quicktags.php&comment_quicktags_remove=none\';
							}
						//-->
						</script>
						';
				exit();
		}
	}

	$lmbbox_comment_quicktags_options_default = array(
		'min_user_level'						=> 7,
		'display_comment_qucktags_toobar'		=> 1,
		'buttons'		=> array(
								array(true, 'ed_strong', 'B', '<strong>', '</strong>', 'b', ''),
								array(true, 'ed_em', 'I', '<em>', '</em>', 'i', ''),
								array(true, 'ed_block', 'B-Quote', '\n\n<blockquote>', '</blockquote>\n\n', 'q', ''),
								array(true, 'ed_pre', 'Code', '<code>', '</code>', 'c', ''),
								array(true, 'ed_link', 'Link', '', '</a>', 'a', ''),
								array(false, 'ed_img', 'Img', '', '', 'm', -1)
							)
	);

	if (isset($_POST['lmbbox_comment_quicktags_options_update'])) {
		$lmbbox_comment_quicktags_updated_options = $_POST;
		unset($lmbbox_comment_quicktags_updated_options['lmbbox_comment_quicktags_options_update']);
		unset($lmbbox_comment_quicktags_updated_options['Submit']);
		$lmbbox_button_update = $lmbbox_comment_quicktags_updated_options['buttons'];
		unset($lmbbox_comment_quicktags_updated_options['buttons']);
		$lmbbox_add_button = $lmbbox_comment_quicktags_updated_options['add_button'];
		unset($lmbbox_comment_quicktags_updated_options['add_button']);
		foreach ($lmbbox_button_update as $button => $settings) {
			if ($settings[1] != '' && $settings[2] != '' && !$settings['remove']) {
				$lmbbox_comment_quicktags_updated_options['buttons'][$button] = array($settings[0], $settings[1], $settings[2], $settings[3], $settings[4], $settings[5], $settings[6]);
			}
		}
		if ($lmbbox_add_button[1] != '' && $lmbbox_add_button[2] != '') {
			$lmbbox_comment_quicktags_updated_options['buttons'][] = array($lmbbox_add_button[0], $lmbbox_add_button[1], $lmbbox_add_button[2], $lmbbox_add_button[3], $lmbbox_add_button[4], $lmbbox_add_button[5], $lmbbox_add_button[6]);
		}
		update_option('lmbbox_comment_quicktags_options', $lmbbox_comment_quicktags_updated_options);
	}
	// Get and set settings
	if (get_option('lmbbox_comment_quicktags_options')) {
		$lmbbox_comment_quicktags_options = get_option('lmbbox_comment_quicktags_options');
	} else {
		add_option('lmbbox_comment_quicktags_options');
		update_option('lmbbox_comment_quicktags_options', $lmbbox_comment_quicktags_options_default);
		$lmbbox_comment_quicktags_options = get_option('lmbbox_comment_quicktags_options');
	}
	// Move Quicktag Button Up or Down in the Display
	if ($_GET['lmbbox_comment_quicktags_move_action'] == 'up' && isset($_GET['lmbbox_comment_quicktags_button'])) {
		$lmbbox_comment_quicktags_move_button = $lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button']];
		$lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button']] = $lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button'] - 1];
		$lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button'] - 1] = $lmbbox_comment_quicktags_move_button;
		update_option('lmbbox_comment_quicktags_options', $lmbbox_comment_quicktags_options);
	} elseif ($_GET['lmbbox_comment_quicktags_move_action'] == 'down' && isset($_GET['lmbbox_comment_quicktags_button'])) {
		$lmbbox_comment_quicktags_move_button = $lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button']];
		$lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button']] = $lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button'] + 1];
		$lmbbox_comment_quicktags_options['buttons'][$_GET['lmbbox_comment_quicktags_button'] + 1] = $lmbbox_comment_quicktags_move_button;
		update_option('lmbbox_comment_quicktags_options', $lmbbox_comment_quicktags_options);
	}

	// Add all content below
	// lmbbox_comment_quicktags() -> loads quicktags into comment forum area
	function lmbbox_comment_quicktags() {
		global $lmbbox_comment_quicktags_options;
		// Browser detection sucks, but until Safari supports the JS needed for this to work people just assume it's a bug in WP
		if ($lmbbox_comment_quicktags_options['display_comment_qucktags_toobar'] && !strstr($_SERVER['HTTP_USER_AGENT'], 'Safari')) { 
?>
		<div id="comment_quicktags">
			<a href="http://wordpress.org/docs/reference/post/#quicktags" title="Help with quicktags">Quicktags</a>:
			<script src="<?php echo get_settings('siteurl') . '/wp-content/plugins/lmbbox-comment-quicktags.php'; ?>" type="text/javascript"></script>
			<script type="text/javascript">edToolbar();</script>
		</div>
<?php
		}
		// defines the textfield name for js
		function lmbbox_comment_quicktags_define_textfield() {	
			echo '
				<script type="text/javascript">
				<!--
				edCanvas = document.getElementById(\'comment\');
				//-->
				</script>
				';
		}
		if ($lmbbox_comment_quicktags_options['display_comment_qucktags_toobar']) { add_action('comment_form', 'lmbbox_comment_quicktags_define_textfield'); }
	}


	// Smileys Options Page
	function lmbbox_comment_quicktags_options_page() {
		global $lmbbox_comment_quicktags_options;
		if (isset($_POST['lmbbox_comment_quicktags_options_update'])) {
			echo '<div class="updated"><p><strong>LMB^Box Comment Quicktags Options Updated.</strong></p></div>';
		}
		if (isset($_GET['lmbbox_comment_quicktags_move_action'])) {
			echo '<div class="updated"><p><strong>LMB^Box Comment Quicktags Button Moved.</strong></p></div>';
		}
?>
	<div class="wrap">
	<h2>LMB^Box Comment Quicktags Options</h2>
	<form name="lmbbox_comment_quicktags_options" method="post">
	<input type="hidden" name="lmbbox_comment_quicktags_options_update" value="update" />
	<fieldset class="options">
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">

      <tr>
        <th valign="top" scope="row">Minimum Level to Change LMB^Box Comment Quicktags Settings :</th>
        <td>
		  <select name="min_user_level">
<?php
	for ($i = 1; $i <= 10; $i++) {
		echo '<option value="' . $i . '" ';
		if ($lmbbox_comment_quicktags_options['min_user_level'] == $i) { echo 'selected '; }
		echo '>' . $i . '</option>';
	}
?>
		  </select>
		</td>
      </tr>
      <tr>
        <th valign="top" scope="row">Display LMB^Box Comment Quicktags Toolbar in Comment Form:</th>
        <td>
		  <label><input type="radio" name="display_comment_qucktags_toobar" value="1" <?php if ($lmbbox_comment_quicktags_options['display_comment_qucktags_toobar']) { echo 'checked '; } ?>/> Yes</label><br />
		  <label><input type="radio" name="display_comment_qucktags_toobar" value="0" <?php if (!$lmbbox_comment_quicktags_options['display_comment_qucktags_toobar']) { echo 'checked '; } ?>/> No</label><br />
		</td>
      </tr>
      <tr>
        <th valign="top" scope="row">Comment Quicktags Toobar Display Settings :</th>
        <td>Select which Comment Quicktag Buttons to display for the Comment Form.<br />
		  <table cellspacing="0" cellpadding="3" border="1" class="editform">
		    <tr bgcolor="#CCCCCC" align="center">
			  <td>Button Display Order</td>
			  <td>Button ID</td>
			  <td>Button Lable</td>
			  <td>Button Open Tag</td>
			  <td>Button Close Tag</td>
			  <td>Button Keyboard Shortcut</td>
			  <td>Button Doesn't Need Close Tag</td>
			  <td>Display Button</td>
			  <td>Remove Button!</td>
			</tr>
<?php
		foreach ($lmbbox_comment_quicktags_options['buttons'] as $button => $settings) {
?>
		    <tr align="center">
			  <td><?php if ($button != 0) { ?><a href="?page=lmbbox-comment-quicktags.php&lmbbox_comment_quicktags_move_action=up&lmbbox_comment_quicktags_button=<?php echo $button; ?>" title="Move Button Display Up">Up</a><?php } ?><?php if ($button != 0 && (count($lmbbox_comment_quicktags_options['buttons']) != $button + 1)) { ?> / <?php } ?><?php if (count($lmbbox_comment_quicktags_options['buttons']) != $button + 1) { ?><a href="?page=lmbbox-comment-quicktags.php&lmbbox_comment_quicktags_move_action=down&lmbbox_comment_quicktags_button=<?php echo $button; ?>" title="Move Button Display Down">Down</a><?php } ?></td>
			  <td><input type="text" name="buttons[<?php echo $button; ?>][1]" size="10" value="<?php echo $settings[1]; ?>" /></td>
			  <td><input type="text" name="buttons[<?php echo $button; ?>][2]" size="10" value="<?php echo $settings[2]; ?>" /></td>
			  <td><input type="text" name="buttons[<?php echo $button; ?>][3]" size="10" value="<?php echo $settings[3]; ?>" /></td>
			  <td><input type="text" name="buttons[<?php echo $button; ?>][4]" size="10" value="<?php echo $settings[4]; ?>" /></td>
			  <td><input type="text" name="buttons[<?php echo $button; ?>][5]" size="10" value="<?php echo $settings[5]; ?>" /></td>
			  <td><input type="checkbox" name="buttons[<?php echo $button; ?>][6]" value="-1" <?php if ($settings[6]) { echo 'checked '; } ?>/></td>
			  <td><input type="checkbox" name="buttons[<?php echo $button; ?>][0]" value="true" <?php if ($settings[0]) { echo 'checked '; } ?>/></td>
			  <td><input type="checkbox" name="buttons[<?php echo $button; ?>][remove]" value="true" /></td>
			</tr>
<?php
		}
?>
		    <tr align="center">
			  <td>Added to the End</td>
			  <td><input type="text" name="add_button[1]" size="10" value="" /></td>
			  <td><input type="text" name="add_button[2]" size="10" value="" /></td>
			  <td><input type="text" name="add_button[3]" size="10" value="" /></td>
			  <td><input type="text" name="add_button[4]" size="10" value="" /></td>
			  <td><input type="text" name="add_button[5]" size="10" value="" /></td>
			  <td><input type="checkbox" name="add_button[6]" value="-1" /></td>
			  <td><input type="checkbox" name="add_button[0]" value="true" /></td>
			  <td>Nope!</td>
			</tr>
		  </table>
		</td>
      </tr>
    </table> 
	</fieldset>
	<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>
	</form> 
	</div>
<?php
	}
	function add_lmbbox_comment_quicktags_options_page() {
		global $lmbbox_comment_quicktags_options;
		add_options_page('LMB^Box Comment Quicktags', 'LMB^Box Comment Quicktags', $lmbbox_comment_quicktags_options['min_user_level'], 'lmbbox-comment-quicktags.php', 'lmbbox_comment_quicktags_options_page');
	}
	add_action('admin_menu', 'add_lmbbox_comment_quicktags_options_page');

} else {
	// call {root}/config.php
	// neat little test to find the wp-config.php file in the root directory
	// from Owen Winkler's Exhibit http://www.asymptomatic.net/wp-hacks/
	$config_file = 'wp-config.php';
	$c = 0;
	while(!is_file($config_file)) {
		$config_file = '../' . $config_file;
		$c++;
		if($c == 15)
			exit('Could not find wp-config.php!');
	}
	require_once($config_file);
	$lmbbox_comment_quicktags_options = get_option('lmbbox_comment_quicktags_options');

// create js script output below!

?>

var edButtons = new Array();
var edOpenTags = new Array();

function edButton(id, display, tagStart, tagEnd, access, open) {
	this.id = id;				// used to name the toolbar button
	this.display = display;		// label on button
	this.tagStart = tagStart; 	// open tag
	this.tagEnd = tagEnd;		// close tag
	this.access = access;		// access key
	this.open = open;			// set to -1 if tag does not need to be closed
}

function zeroise(number, threshold) {
	// FIXME: or we could use an implementation of printf in js here
	var str = number.toString();
	if (number < 0) { str = str.substr(1, str.length) }
	while (str.length < threshold) { str = "0" + str }
	if (number < 0) { str = '-' + str }
	return str;
}

var now = new Date();
var datetime = now.getUTCFullYear() + '-' + 
zeroise(now.getUTCMonth() + 1, 2) + '-' +
zeroise(now.getUTCDate(), 2) + 'T' + 
zeroise(now.getUTCHours(), 2) + ':' + 
zeroise(now.getUTCMinutes(), 2) + ':' + 
zeroise(now.getUTCSeconds() ,2) +
'+00:00';

<?php
	foreach ($lmbbox_comment_quicktags_options['buttons'] as $button => $settings) {
		if ($settings[0]) {
			echo 	"edButtons[edButtons.length] = new edButton(
																'$settings[1]',
																'$settings[2]',
																'$settings[3]',
																'$settings[4]',
																'$settings[5]',
																'$settings[6]'
															);
					";
		}
	}
?>

// Start code
function edShowButton(button, i) {
	if (button.id == 'ed_img') {
		document.write('<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="edInsertImage(edCanvas);" value="' + button.display + '" />');
	}
	else if (button.id == 'ed_link') {
		document.write('<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="edInsertLink(edCanvas, ' + i + ');" value="' + button.display + '" />');
	}
	else {
		document.write('<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="edInsertTag(edCanvas, ' + i + ');" value="' + button.display + '"  />');
	}
}

function edAddTag(button) {
	if (edButtons[button].tagEnd != '') {
		edOpenTags[edOpenTags.length] = button;
		document.getElementById(edButtons[button].id).value = '/' + document.getElementById(edButtons[button].id).value;
	}
}

function edRemoveTag(button) {
	for (i = 0; i < edOpenTags.length; i++) {
		if (edOpenTags[i] == button) {
			edOpenTags.splice(i, 1);
			document.getElementById(edButtons[button].id).value = 		document.getElementById(edButtons[button].id).value.replace('/', '');
		}
	}
}

function edCheckOpenTags(button) {
	var tag = 0;
	for (i = 0; i < edOpenTags.length; i++) {
		if (edOpenTags[i] == button) {
			tag++;
		}
	}
	if (tag > 0) {
		return true; // tag found
	}
	else {
		return false; // tag not found
	}
}	

function edCloseAllTags() {
	var count = edOpenTags.length;
	for (o = 0; o < count; o++) {
		edInsertTag(edCanvas, edOpenTags[edOpenTags.length - 1]);
	}
}

function edToolbar() {
	document.write('<div id="ed_comment_toolbar">');
	for (i = 0; i < edButtons.length; i++) {
		edShowButton(edButtons[i], i);
	}
	document.write('<input type="button" id="ed_close" class="ed_button" onclick="edCloseAllTags();" title="Close all open tags" value="Close Tags" />');
	document.write('</div>');
}

// insertion code

function edInsertTag(myField, i) {
	//IE support
	if (document.selection) {
		myField.focus();
	    sel = document.selection.createRange();
		if (sel.text.length > 0) {
			sel.text = edButtons[i].tagStart + sel.text + edButtons[i].tagEnd;
		}
		else {
			if (!edCheckOpenTags(i) || edButtons[i].tagEnd == '') {
				sel.text = edButtons[i].tagStart;
				edAddTag(i);
			}
			else {
				sel.text = edButtons[i].tagEnd;
				edRemoveTag(i);
			}
		}
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var cursorPos = endPos;
		var scrollTop = myField.scrollTop;

		if (startPos != endPos) {
			myField.value = myField.value.substring(0, startPos)
			              + edButtons[i].tagStart
			              + myField.value.substring(startPos, endPos) 
			              + edButtons[i].tagEnd
			              + myField.value.substring(endPos, myField.value.length);
			cursorPos += edButtons[i].tagStart.length + edButtons[i].tagEnd.length;
		}
		else {
			if (!edCheckOpenTags(i) || edButtons[i].tagEnd == '') {
				myField.value = myField.value.substring(0, startPos) 
				              + edButtons[i].tagStart
				              + myField.value.substring(endPos, myField.value.length);
				edAddTag(i);
				cursorPos = startPos + edButtons[i].tagStart.length;
			}
			else {
				myField.value = myField.value.substring(0, startPos) 
				              + edButtons[i].tagEnd
				              + myField.value.substring(endPos, myField.value.length);
				edRemoveTag(i);
				cursorPos = startPos + edButtons[i].tagEnd.length;
			}
		}
		myField.focus();
		myField.selectionStart = cursorPos;
		myField.selectionEnd = cursorPos;
		myField.scrollTop = scrollTop;
	}
	else {
		if (!edCheckOpenTags(i) || edButtons[i].tagEnd == '') {
			myField.value += edButtons[i].tagStart;
			edAddTag(i);
		}
		else {
			myField.value += edButtons[i].tagEnd;
			edRemoveTag(i);
		}
		myField.focus();
	}
}

function edInsertContent(myField, myValue) {
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		              + myValue 
                      + myField.value.substring(endPos, myField.value.length);
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
	} else {
		myField.value += myValue;
		myField.focus();
	}
}

function edInsertLink(myField, i, defaultValue) {
	if (!defaultValue) {
		defaultValue = 'http://';
	}
	if (!edCheckOpenTags(i)) {
		var URL = prompt('Enter the URL' ,defaultValue);
		if (URL) {
			edButtons[i].tagStart = '<a href="' + URL + '">';
			edInsertTag(myField, i);
		}
	}
	else {
		edInsertTag(myField, i);
	}
}

function edInsertLink(myField, i, defaultValue) {
	if (!defaultValue) {
		defaultValue = 'http://';
	}
	if (!edCheckOpenTags(i)) {
		var URL = prompt('Enter the URL' ,defaultValue);
		if (URL) {
			edButtons[i].tagStart = '<a href="' + URL + '">';
			edInsertTag(myField, i);
		}
	}
	else {
		edInsertTag(myField, i);
	}
}

function edInsertImage(myField) {
	var myValue = prompt('Enter the URL of the image', 'http://');
	if (myValue) {
		myValue = '<img src="' 
				+ myValue 
				+ '" alt="' + prompt('Enter a description of the image', '') 
				+ '" />';
		edInsertContent(myField, myValue);
	}
}

<?php
}
?>