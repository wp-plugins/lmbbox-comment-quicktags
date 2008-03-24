=== LMB^Box Comment Quicktags ===
Contributors: lmbbox
Donate link: 
Tags: comment, quicktags, buttons, formating
Requires at least: 2.3.2
Tested up to: 2.3.2
Stable tag: 2.4

Inserts a quicktag toolbar in the blog comment form.

== Description ==

LMB^Box Comment Quicktags is a plugin that puts a Quicktag Toolbar, just like the admin's 
Quicktags Toolbar, right above the comments text form area. You can use the bar to add 
strong/bold, em/italic, code, blockquote, and links tags by default. You can add more or 
remove some if you do not want them there. This allows general users/guests to be able to 
post comments with advanced editing features.

== Installation ==

1. Copy the file lmbbox-comment-quicktags.php into your wp-content/plugins directory
2. Activate this plugin in the WordPress Admin Panel
3. In your comments.php theme file add 
	"<?php if (function_exists('lmbbox_comment_quicktags_display')) { lmbbox_comment_quicktags_display(); } ?>" 
	right above the <textarea name="comment" id="comment" ....></textarea>. I put mine just 
	above my commented out allowed_tags() call, example below.

	-- Example --
		<p>
			<?php if (function_exists('lmbbox_comment_quicktags_display')) { lmbbox_comment_quicktags_display(); } ?>
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
4. The code added at the lmbbox_comment_quicktags() function call has some CSS styling tags 
	in place. Below is what I have for my style settings, which mimic the admin's quicktags 
	look. There is a span with an id of comment_quicktags that wraps the whole of the code. 
	Then in the quicktags script, a link (<a href="http://codex.wordpress.org/index.php/Write_Post_SubPanel#Quicktags" title="Help With Quicktags">Quicktags</a>:) 
	is displayed followed by a span with an id of ed_comment_toolbar. The ID of the span can 
	be used by anybody who may want to add more buttons after the hard coded buttons 
	(eg. My plugin -> LMB^Box Smileys Plugin). All of the buttons are inputs with 
	a class of ed_button, and all of the buttons have ids of ed_%Name_of_button%. The build 
	in button ids are: ed_strong, ed_em, ed_pre, ed_block, and ed_link. 

/*---------------------- Comment Quicktags ---------------------------*/
/* Main Span */
#comment_quicktags {
	text-align: left;
	margin-left: 1%;
}
/* Button Style */
#comment_quicktags input.ed_button {
	background: #F4F4F4;
	border: 1px solid #D6D3CE;
	color: #000000;
	font-family: Georgia, "Times New Roman", Times, serif;
	margin: 1px;
	width: auto;
}
/* Button Style on focus/click */
#comment_quicktags input:focus.ed_button {
	background: #FFFFFF;
	border: 1px solid #686868;
}
/* Button Lable style */
#comment_quicktags #ed_strong {
	font-weight: bold;
}
/* Button Lable style */
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

== Frequently Asked Questions ==

= Do I really need to use this plugin? =

You don't HAVE to have this plugin, but then you, your users, and your guests won't be 
able to make use of any of the advanced formating features UNLESS you/they now how to 
code it in manually.

== Screenshots ==

1. Comment Section Form
2. Admin Option Page

== Arbitrary section ==

== Change Log ==


	= 2.4 =

	Final Release!
	This Version Is For WP 1.5 up to 2.0, Anything Over 2.0 May Or May Not Work!
	Starting Work On New Plugin, LMB^Box Comment Editor That Will Take Over.
	Updated Code And Class To Use Better Features.
	Removed Documentation From Plugin File. Now Only Located In readme.txt File.
	Updated Quicktags Help Link.
	Changed Options Page Name From LMB^Box Comment Quicktags To Comment Quicktags.

	= 2.3 =

	Changed get_settings('home') To get_settings('siteurl').
	Added Header Output For Directly Accessed Javascript (Thanks To rudd-o AT rudd-o DOT com).

	= 2.2 =

	Fixed Slow Loading Problems Of Toolbar.
	Updated Plugin Version Notifier Code.

	= 2.1 =

	Fixed Plugin's XHTML Validation, Now Validates XHTML 1.0 Transitional.
	Added Plugin Version Notifier.

	= 2.0 =

	Rewrote plugin into a PHP Class.
	Fixed magic_quotes adding '\' to button options.
	Optimized plugin code.
	Fixed updating options and moving button problems.
	NOTE: Function call has changed! Make sure you read Step 3!

	= 1.7.2 =

	Removed some old ToDo comments.
	Removed Safari brower check (Quicktags should work fine in Safari).

	= 1.7.1 =

	Fixed the version #'s (Doh!) in the file (thanks to Kenny for telling). 

	= 1.7 =

	Found and fixed some wrong code in the deactivation script to ask to remove DB entries.

	= 1.6 =

	Added Options Page for plugin's settings!
	Added ability to add/edit/manage/remove Quicktags Buttons.
	Added ability to disable display of Comment Quicktags Toolbar without removing plugin 
		or code from your comments.php file.

	= 1.5 =

	Plugin's Name Changed! From WP Comment Quicktags Plus to LMB^Box Comment Quicktags!

	= 1.4 =

	Added Steps 3.1 and 3.2 documentation.

	= 1.3 =

	I fixed a problem with the style sheet and the display of the quicktags bar:
		The styles where definded as classes and the main div used a class of 
		comment_quicktags, but for some reason the styles for the input buttons 
		didn't pass through the main div (comment_quicktags) to the quicktags toolbar 
		div (id="ed_comment_quicktags"). I first just added "#ed_comment_quicktags" in 
		between the ".comment_quicktags" and "input.ed_button" / "input:focus.ed_button" 
		styles, and this worked just fine. I didn't know why this was happening. I then 
		tried changing the class comment_quicktags to an ID (#) instead (I also removed 
		what I just added so that it was back to the way it was). I edited the plugin 
		file to change the main div from a class of comment_quicktags to an id of 
		comment_quicktags so that the styles will work. Guess what? No problems with 
		the input styles not passing through the main div now! I don't know why this is 
		but it works! So I figured that it would be better to use the id (#) version of 
		the fix instead of adding "#ed_comment_quicktags" in between the 
		".comment_quicktags" and "input.ed_button" / "input:focus.ed_button" styles. 
		This way if anybody really needs to add a button to the main div and not the 
		quicktags toolbar div, then the styles will still work correctly. By the way, 
		if anybody knows why this happened, please leave a comment on my blog at 
		http://aboutme.lmbbox.com.

	= 1.2 =

	I have now included a readme.txt file with the plugin and I have also edited the 
		documentation in the file.
	Added button template code and documentation for adding new buttons.

	= 1.1 =

	The first offical release of WP Comment Quicktags Plus for WordPress!
