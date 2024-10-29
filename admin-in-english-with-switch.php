<?php
/*
Plugin Name: Admin in English with Switch
Plugin URI: http://wordpress.org/extend/plugins/admin-in-english-with-switch/
Description: This plugin is an extended version of Admin in English plugin (v1.2) developed by Nikolay Bachiyski. It lets you switch your backend administration panel to English from admin toolbar, even if the rest of your blog is translated into another language. It turns English language on and off for each user independently.
Version: 1.1
Author: Armen Danielyan
Tags: translation, translations, i18n, admin, english, localization, backend
*/

/* Fixing the "Call to undefined function wp_get_current_user()" error */
if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {
	require_once(ABSPATH . 'wp-includes/pluggable.php');
}

/* Getting plugin settings for current user */
$current_user = wp_get_current_user();
if(get_user_meta($current_user->ID, 'enabled-admin-in-english', true)=='') {
	add_user_meta($current_user->ID, 'enabled-admin-in-english', 'true', true);
}

/* Add menu on toolbar */
function load_switcher_script() {
	wp_enqueue_script(
		'newscript',
		plugins_url('/js/switcher.js', __FILE__),
		array('scriptaculous')
	);
}    
 
add_action('init', 'load_switcher_script');

if(is_admin()) {
	add_action('admin_bar_menu', 'add_lang_selector', 100);
}

function add_lang_selector($admin_bar)
{
	global $wp_admin_bar;
	$url_action = admin_url( 'admin-ajax.php' );

	$wp_admin_bar->add_node( array(
		'id'    => 'english',
		'title' => 'English',
		'href'  => '#',
		'meta'  => array(
			'title' => __('English')
		),
	));

	$wp_admin_bar->add_node( array(
		'id'    => 'enable',
		'parent' => 'english',
		'title' => 'Enable',
		'href'  => '#',
		'meta'  => array(
			'title' => __('Enable'),
			'class' => 'my_menu_item_class',
			'onclick' => 'jsEnableEng("true")'
		),
	));
	
	$wp_admin_bar->add_node( array(
		'id'    => 'disable',
		'parent' => 'english',
		'title' => 'Disable',
		'href'  => '#',
		'meta'  => array(
			'title' => __('Disable'),	
			'class' => 'my_menu_item_class',
			'onclick' => 'jsEnableEng("false")'			
		),
	));
}
//end of toolbar

function admin_in_english_locale( $locale ) {
	if ( admin_in_english_should_use_english() ) {
		return 'en_US';
	}
	return $locale;
}

function admin_in_english_should_use_english() {
	// frontend AJAX calls are mistakend for admin calls, because the endpoint is wp-admin/admin-ajax.php
	return admin_in_english_is_admin() && !admin_in_english_is_frontend_ajax();
}

function admin_in_english_is_admin() {
	return
		is_admin() || admin_in_english_is_tiny_mce() || admin_in_english_is_login_page();
}

function admin_in_english_is_frontend_ajax() {
	return defined( 'DOING_AJAX' ) && DOING_AJAX && false === strpos( wp_get_referer(), '/wp-admin/' );
}

function admin_in_english_is_tiny_mce() {
	return false !== strpos( $_SERVER['REQUEST_URI'], '/wp-includes/js/tinymce/');
}

function admin_in_english_is_login_page() {
	return false !== strpos( $_SERVER['REQUEST_URI'], '/wp-login.php' );
}

add_action('wp_ajax_enable_eng', 'enable_eng');

/* Enabling English backend*/
if(get_user_meta($current_user->ID, 'enabled-admin-in-english', true) == 'true') {
	add_filter( 'locale', 'admin_in_english_locale' );
}

function enable_eng() {
	$current_user = wp_get_current_user();
	if (isset($_POST['data'])) {
	     if($_POST['data'] == 'true') {
		update_user_meta($current_user->ID, 'enabled-admin-in-english', 'true');
	     }
	     if($_POST['data'] == 'false') {
		update_user_meta($current_user->ID, 'enabled-admin-in-english', 'false');	     }		
 	}
	die(); // this is required to return a proper result
}