<?php
/*
Plugin Name: LMB^Box Comment Quicktags
Plugin URI: http://aboutme.lmbbox.com/
Description: Inserts a quicktag toolbar in the blog comment form.
Version: 1.5
Author: Thomas Montague
Author URI: http://aboutme.lmbbox.com
*/ 
/*
LMB^Box Comment Quicktags -> Was WP Comment Quicktags Plus!
version 1.5, 2005-05-17
By Thomas Montague

This Plugin is modified code from Owen Winkler's Comment Quicktags Plugin
****** Plugin Info ********
Plugin Name: Comment Quicktags
Plugin URI: http://www.asymptomatic.net/wp-hacks
Description: Inserts a quicktag toolbar on the blog comment form.
Version: 1.0
Author: Owen Winkler
Author URI: http://www.asymptomatic.net

LMB^Box Comment Quicktags Plug is a plugin that puts a Quicktag Menu bar, just like the admin's 
Quicktags Menu bar, right above the comments text form area. You can use the bar to add 
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
	(eg. My plugin -> LMB^Box Smileys Plugin). All of the buttons are inputs with 
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
5. If you wish to change/edit the Quicktag Buttons that are displayed, you will need to edit 
	the wp-comment-quicktags-plus.php file. Open the file and find "// edit the buttons here" 
	on about line 141. Below you will see all of the buttons definded. To remove a button, 
	simple either remove the code for it, or comment out the code with "/* code_is_here '/*' ->reversed!". 
	If you want to add a button, copy the button template below the "// edit the buttons here" 
	line. Then paste it where you want to button to appear (the buttons are in the order 
	in which they are entered, so if the "strong" button is after the "em" button, then 
	the "em" button will be before the "strong" button). Then edit the code to the right 
	values and remove the "// info" parts so that you have a button code that looks like 
	the rest. If you want to, add a "// info for you about button" to remind you what you 
	added and what to save if you upgrade to a new version of LMB^Box Comment Quicktags Plus.


== End of Installation ==
*/

if(defined('ABSPATH')) {
	// BOF -- lmbbox_comment_quicktags() -> loads quicktags into comment forum area
	function lmbbox_comment_quicktags() {
		// Browser detection sucks, but until Safari supports the JS needed for this to work people just assume it's a bug in WP
		if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Safari')) { 
?>
		<div id="comment_quicktags">
			<a href="http://wordpress.org/docs/reference/post/#quicktags" title="Help with quicktags">Quicktags</a>:
			<script src="<?php echo get_settings('siteurl') . '/wp-content/plugins/lmbbox-comment-quicktags.php'; ?>" type="text/javascript"></script>
			<script type="text/javascript">edToolbar();</script>
		</div>
<?php
		}
	}
	// EOF -- lmbbox_comment_quicktags() -> loads quicktags into comment forum area

	function lmbbox_comment_quicktags_define_textfield() {	
		echo '
			<script type="text/javascript">
			<!--
			edCanvas = document.getElementById(\'comment\');
			//-->
			</script>
			';
	}
	add_action('comment_form', 'lmbbox_comment_quicktags_define_textfield');

} else {

?>

var edButtons = new Array();
var edLinks = new Array();
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
var datetime = now.getFullYear() + '-' + 
				zeroise(now.getMonth() + 1, 2) + '-' +
				zeroise(now.getDate(), 2) + 'T' + 
				zeroise(now.getHours(), 2) + ':' + 
				zeroise(now.getMinutes(), 2) + ':' + 
				zeroise(now.getSeconds() ,2) +
				// FIXME: we could try handling timezones like +05:30 and the like
				zeroise((now.getTimezoneOffset()/60), 2) + ':' + '00';

// edit the buttons here -> Template button below
/*
new edButton('button_id',		// used to name the toolbar button
'button_label',					// label on button
'button_open_tag',				// open tag
'button_close_tag',				// close tag
'button_keyboard_shortcut',		// access key
'does_not_need_close_tag'		// set to -1 if tag does not need to be closed -> generally, you can just omit this last 
								// one but if you do, REMOVE the ',' from the previous line!
);
*/
edButtons[edButtons.length] = 
new edButton('ed_strong',
'str',
'<strong>',
'</strong>',
'b'
);

edButtons[edButtons.length] = 
new edButton('ed_em',
'em',
'<em>',
'</em>',
'i'
);

edButtons[edButtons.length] = 
new edButton('ed_pre',
'code',
'<code>',
'</code>',
'c'
);

edButtons[edButtons.length] = 
new edButton('ed_block',
'b-quote',
'<blockquote>',
'</blockquote>',
'q'
);

edButtons[edButtons.length] = 
new edButton('ed_link',
'link',
'',
'</a>',
'a'
); // special case

function edLink() {
	this.display = '';
	this.URL = '';
	this.newWin = 0;
}

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

<?php
}
?>