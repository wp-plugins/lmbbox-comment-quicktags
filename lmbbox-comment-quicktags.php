<?php
/*
Plugin Name: LMB^Box Comment Quicktags
Plugin URI: http://aboutme.lmbbox.com/lmbbox-plugins/lmbbox-comment-quicktags/
Description: Inserts a quicktag toolbar in the blog comment form.
Version: 2.4
Author: Thomas Montague
Author URI: http://aboutme.lmbbox.com

LMB^Box Comment Quicktags -> Was WP Comment Quicktags Plus!
version 2.4, 2006-02-08
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
*/

// If Loaded By WordPress And Not Directly Accessed
if (defined('ABSPATH')) {
	// BEGIN - Class lmbbox_comment_quicktags_class
	class lmbbox_comment_quicktags_class {
		var $plugin_info = array(
				'file'			=> 'lmbbox-comment-quicktags.php',
				'name'			=> 'Comment Quicktags',
				'version'		=> '2.4',
				'by'			=> 'LMB^Box',
				'title'			=> 'LMB^Box Comment Quicktags Version 2.4',
				'options'		=> 'lmbbox_comment_quicktags_options'
			);
		var $options = array(
				'user_level'	=> 7,
				'display'		=> 1, // 0 = Off, 1 = On
				'buttons'		=> array(
										array(true, 'ed_strong', 'B', '<strong>', '</strong>', 'b', ''),
										array(true, 'ed_em', 'I', '<em>', '</em>', 'i', ''),
										array(true, 'ed_block', 'B-Quote', '\n\n<blockquote>', '</blockquote>\n\n', 'q', ''),
										array(true, 'ed_pre', 'Code', '<code>', '</code>', 'c', ''),
										array(true, 'ed_link', 'Link', '', '</a>', 'a', ''),
										array(false, 'ed_img', 'Img', '', '', 'm', -1)
									)
			);

		// Class Constructor
		function lmbbox_comment_quicktags_class() {
			// Setting Remove Activation
			if (strstr($_SERVER['PHP_SELF'], 'plugins.php') && $_GET['action'] == 'deactivate' && $_GET['plugin'] == $this->plugin_info['file']) { return $this->deactivate(); }
			add_action('activate_' . $this->plugin_info['file'], array(&$this, 'activate'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('wp_head', array(&$this, 'wp_head'));
			add_action('comment_form', array(&$this, 'define_textfield'));

			// Activate setting updates and get settings
			$this->get_options();
		}

		// Setting Removal Code on Deactivation of Plugin!
		function deactivate() {
			switch ($_GET['comment_quicktags_remove']) {
				case 'options':
					delete_option($this->plugin_info['options']);
					break;
				case 'none':
					break;
				default:
					echo 	'
							<script language="JavaScript" type="text/javascript">
							<!--
								var remove_options = confirm(\'Do you wish to remove your LMB^Box Comment Quicktags Option Settings?\');
								if (remove_options) {
									window.location = \'plugins.php?action=deactivate&plugin=' . $this->plugin_info['file'] . '&comment_quicktags_remove=options\';
								} else {
									window.location = \'plugins.php?action=deactivate&plugin=' . $this->plugin_info['file'] . '&comment_quicktags_remove=none\';
								}
							//-->
							</script>
							';
					exit();
			}
		}

		// Activate LMB^Box Comment Quicktags By Adding Options Settings
		function activate() { add_option($this->plugin_info['options'], $this->options, $this->plugin_info['title']); }

		// Get LMB^Box Comment Quicktags Settings
		function get_options() {
			$options = get_option($this->plugin_info['options']);
			if ($options !== false) {
				$this->options = $options;
			} else { $this->activate(); }
		}

		// Update LMB^Box Comment Quicktags Options Settings
		function update_options() {
			$this->options = $_POST;
			unset($this->options['lmbbox_comment_quicktags_options_update']);
			unset($this->options['Submit']);
			$updated_buttons = $this->options['buttons'];
			unset($this->options['buttons']);
			$add_button = $this->options['add_button'];
			unset($this->options['add_button']);
			foreach ($updated_buttons as $button => $settings) {
				if ($settings[1] != '' && $settings[2] != '' && !$settings['remove']) {
					$this->options['buttons'][$button] = (get_magic_quotes_gpc) ? array($settings[0], stripslashes($settings[1]), stripslashes($settings[2]), stripslashes($settings[3]), stripslashes($settings[4]), $settings[5], $settings[6]) : array($settings[0], $settings[1], $settings[2], $settings[3], $settings[4], $settings[5], $settings[6]);
				}
			}
			if ($add_button[1] != '' && $add_button[2] != '') {
				$this->options['buttons'][] = (get_magic_quotes_gpc) ? array($add_button[0], stripslashes($add_button[1]), stripslashes($add_button[2]), stripslashes($add_button[3]), stripslashes($add_button[4]), $add_button[5], $add_button[6]) : array($add_button[0], $add_button[1], $add_button[2], $add_button[3], $add_button[4], $add_button[5], $add_button[6]);
			}
			update_option($this->plugin_info['options'], $this->options);
		}

		// Move Quicktag Button Up or Down in the Display
		function move_quicktag() {
			if ($_GET['lmbbox_comment_quicktags_move_action'] == 'up') {
				$move_button = $this->options['buttons'][$_GET['lmbbox_comment_quicktags_button']];
				$this->options['buttons'][$_GET['lmbbox_comment_quicktags_button']] = $this->options['buttons'][$_GET['lmbbox_comment_quicktags_button'] - 1];
				$this->options['buttons'][$_GET['lmbbox_comment_quicktags_button'] - 1] = $move_button;
				update_option('lmbbox_comment_editor_options', $this->options);
			} elseif ($_GET['lmbbox_comment_quicktags_move_action'] == 'down') {
				$move_button = $this->options['buttons'][$_GET['lmbbox_comment_quicktags_button']];
				$this->options['buttons'][$_GET['lmbbox_comment_quicktags_button']] = $this->options['buttons'][$_GET['lmbbox_comment_quicktags_button'] + 1];
				$this->options['buttons'][$_GET['lmbbox_comment_quicktags_button'] + 1] = $move_button;
				update_option($this->plugin_info['options'], $this->options);
			}
		}

		function admin_menu() { add_options_page($this->plugin_info['by'] . ' ' . $this->plugin_info['name'], $this->plugin_info['name'], $this->options['user_level'], $this->plugin_info['file'], array(&$this, 'options_page')); }

		function options_page() {
			if ($_POST['lmbbox_comment_quicktags_options_update'] == 'update') {
				$this->update_options();
				echo '<div id="message" class="updated fade"><p><strong>' . $this->plugin_info['by'] . ' ' . $this->plugin_info['name'] . ' Options Updated.</strong></p></div>';
			} elseif (isset($_GET['lmbbox_comment_quicktags_move_action']) && isset($_GET['lmbbox_comment_quicktags_button'])) {
				$this->move_quicktag();
				echo '<div id="message" class="updated fade"><p><strong>' . $this->plugin_info['by'] . ' ' . $this->plugin_info['name'] . ' Button Moved.</strong></p></div>';
			}
			$this->display_options_page();
		}

		// LMB^Box Comment Quicktags Options Page
		function display_options_page() {
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
		  <select name="user_level">
<?php
			for ($i = 1; $i <= 10; $i++) {
				echo '<option value="' . $i . '" ';
				if ($this->options['user_level'] == $i) { echo 'selected '; }
				echo '>' . $i . '</option>';
			}
?>
		  </select>
		</td>
      </tr>
      <tr>
        <th valign="top" scope="row">Display LMB^Box Comment Quicktags Toolbar in Comment Form:</th>
        <td>
		  <label><input type="radio" name="display" value="1" <?php if ($this->options['display']) { echo 'checked '; } ?>/> Yes</label><br />
		  <label><input type="radio" name="display" value="0" <?php if (!$this->options['display']) { echo 'checked '; } ?>/> No</label><br />
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
			foreach ($this->options['buttons'] as $button => $settings) {
?>
		    <tr align="center">
			  <td><?php if ($button != 0) { ?><a href="?page=<?php echo $this->plugin_info['file']; ?>&lmbbox_comment_quicktags_move_action=up&lmbbox_comment_quicktags_button=<?php echo $button; ?>" title="Move Button Display Up">Up</a><?php } ?><?php if ($button != 0 && (count($this->options['buttons']) != $button + 1)) { ?> / <?php } ?><?php if (count($this->options['buttons']) != $button + 1) { ?><a href="?page=<?php echo $this->plugin_info['file']; ?>&lmbbox_comment_quicktags_move_action=down&lmbbox_comment_quicktags_button=<?php echo $button; ?>" title="Move Button Display Down">Down</a><?php } ?></td>
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

		// Load LMB^Box Comment Quicktags into Comment Forum Area
		function display() {
			if ($this->options['display']) { 
?>
		<!-- BEGIN - <?php echo $this->plugin_info['title']; ?> - Display //-->
		<span id="comment_quicktags">
			<a href="http://codex.wordpress.org/index.php/Write_Post_SubPanel#Quicktags" title="Help With Quicktags">Quicktags</a>:
			<script type="text/javascript" language="javascript">
			<!--
<?php
			foreach ($this->options['buttons'] as $button => $settings) {
				if ($settings[0]) {
?>
				edButtons[edButtons.length] = new edButton(
									'<?php echo $settings[1]; ?>',
									'<?php echo $settings[2]; ?>',
									'<?php echo $settings[3]; ?>',
									'<?php echo $settings[4]; ?>',
									'<?php echo $settings[5]; ?>',
									'<?php echo $settings[6]; ?>'
								);
<?php
				}
			}
?>
				edToolbar();
			//-->
			</script>
		</span>
		<!-- END - <?php echo $this->plugin_info['title']; ?> - Display //-->
<?php
			}
		}

		// Add Javascript include 
		function wp_head() {
			if (is_single() || is_page()) {
?>
	<!-- BEGIN - <?php echo $this->plugin_info['title']; ?> //-->
	<script type="text/javascript" language="javascript" src="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/lmbbox-comment-quicktags.php"></script>
	<!-- END - <?php echo $this->plugin_info['title']; ?> //-->
<?php
			}
		}

		// Define Textfield Name for Quicktags JavaScript
		function define_textfield() {	
			if ($this->options['display'] == 1) {
?>
	<!-- BEGIN - <?php echo $this->plugin_info['title']; ?> //-->
	<script type="text/javascript" language="javascript">edCanvas = document.getElementById('comment');</script>
	<!-- END - <?php echo $this->plugin_info['title']; ?> //-->
<?php
			}
		}
	}
	// END - Class lmbbox_comment_quicktags_class

	// LMB^Box Comment Quicktags Display Function!
	function lmbbox_comment_quicktags_display() { $GLOBALS['lmbbox_comment_quicktags']->display(); }

	// BEGIN - LMB^Box Comment Quicktags Activation Calls
	$lmbbox_comment_quicktags = new lmbbox_comment_quicktags_class;
	// END - LMB^Box Comment Quicktags Activation Calls
} else {
	// Directly Accessed
	header("Cache-Control: must-revalidate");
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24 * 30) . ' GMT');
	header('Content-Type: text/javascript');
?>
	// Load Quicktags JavaScript
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
				document.getElementById(edButtons[button].id).value = document.getElementById(edButtons[button].id).value.replace('/', '');
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
		document.write('<span id="ed_comment_toolbar">');
		for (i = 0; i < edButtons.length; i++) {
			edShowButton(edButtons[i], i);
		}
		document.write('<input type="button" id="ed_close" class="ed_button" onclick="edCloseAllTags();" title="Close all open tags" value="Close Tags" />');
		document.write('</span>');
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
