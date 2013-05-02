<?php

if ( !defined('ABSPATH') )
    die('You are not allowed to call this page directly.');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Easy Accordion</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.core.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.widget.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-content/plugins/easy-accordion/tinymce/tinymce.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url("/css/style.css", __FILE__); ?>" media="all" />

  <base target="_self" />
</head>

<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
	
	<div class="panel_wrapper">
		<?php
		global $wpdb;
		$easy_accordion_table = $wpdb->prefix."easy_accordion_slider";
		$easy_accordions = $wpdb->get_results("SELECT * FROM $easy_accordion_table");
		?>
		<p><label for="easy_accordion">Easy Accordion</label> <select id="easy_accordion" name="easy_accordion">
			<option value=""></option>
			<?php foreach ($easy_accordions as $easy_accordion) { ?>
				<option value="[easy-accordion id='<?php echo(str_replace(" ", "_", strtolower($easy_accordion->ID))); ?>'][/easy-accordion]"><?php echo($easy_accordion->accordion_name); ?></option>
			<?php }?>
		</select></p>
		<div class="mceActionPanel">
			<div id="cancel_easy_accordion">
				<input type="button" id="cancel" name="cancel_easy_accordion" value="<?php _e("Cancel", 'easy_accordions'); ?>" onclick="tinyMCEPopup.close();" />
			</div>
			<div id="insert_easy_accordion">
				<input type="submit" id="insert" name="insert_easy_accordion" value="<?php _e("Insert", 'easy_accordions'); ?>" onclick="insertEasyAccordion();" />
			</div>
		</div>
	</div>
</body>
</html>