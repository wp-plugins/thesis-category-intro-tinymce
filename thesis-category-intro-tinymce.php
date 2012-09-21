<?php
/*
Plugin Name: Thesis Archive Intro Visual Editor
Plugin URI: http://andrewsfreeman.com/thesis-category-intro-plugin/
Description: Adds the visual editor to Thesis' category archive intro field.
Version: 1.0
Author: Andrew Freeman
Author URI: http://andrewsfreeman.com
License: GPL2
*/

/*  Copyright 2012 Andrew S Freeman (email: andrew@andrewsfreeman.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Add shortcode support and remove HTML filtering.
add_filter( 'thesis_archive_intro', 'do_shortcode', 100 );
add_action( 'init', 'asf_tai_init' );
function asf_tai_init() {
	if ( is_admin() || defined( 'DOING_AJAX' ) ) {
		if ( current_user_can( 'manage_categories' ) )
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
	}
}

// Ensure that we're on the category and tag pages, then setup everything we need
add_action( 'load-categories.php', 'asf_tai_admin_init' );
add_action( 'load-edit-tags.php', 'asf_tai_admin_init' );
function asf_tai_admin_init() {
	if ( user_can_richedit() && isset( $_GET['action'] ) && 'edit' === $_GET['action'] 
		&& ( !empty( $_GET['cat_ID'] ) || ( !empty( $_GET['taxonomy'] ) && !empty( $_GET['tag_ID'] ) ) ) ) {
		add_action( 'admin_head', 'asf_tai_head' );
		add_action( 'admin_footer', 'wp_tiny_mce' );
		add_action( 'admin_print_footer_scripts', 'asf_tai_print_footer_scripts', 99 );
	}
}

// Add the styles for editor buttons and setup thickbox for tinymce plugins
function asf_tai_head() { 
	wp_enqueue_style( 'editor-buttons' );
	wp_enqueue_script( 'editor' );
	add_thickbox();
}

/**
 * This function outputs the JS for adding tinyMCE and ensuring its cooperation.
 * Added the visual and HTML tabs in case someone wants them, and changed the ID
 * to #content instead of #thesis_content because some plugins rely on that
 * (e.g., Special Text Boxes).
 * @todo  move into its own file?
 */
function asf_tai_print_footer_scripts() {
	?>
	<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($) {
			jQuery('#thesis_content').attr('id','content');
			jQuery("#content").before('<p align="right"><a class="wp-switch-editor toggleVisual">Visual</a><a class="wp-switch-editor toggleHTML">HTML</a></p>');
			$('a.toggleVisual').click(function() {
				tinyMCE.execCommand('mceAddControl', false, 'content');
			});
			$('a.toggleHTML').click(function() {
				tinyMCE.execCommand('mceRemoveControl', false, 'content');
			});
			tinyMCE.execCommand('mceAddControl', true, 'content');
		});
		/* ]]> */
	</script>
	<?php
}
