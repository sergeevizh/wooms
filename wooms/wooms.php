<?php
/*
Plugin Name: WooMS
Description: Integration for WooCommerce and MoySklad (moysklad.ru, МойСклад) via REST API (wooms)
Plugin URI: https://wpcraft.ru/product/wooms/
Author: WPCraft
Author URI: https://wpcraft.ru/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version: 1.7.8
*/



require_once 'inc/class-import-products-walker.php';
require_once 'inc/class-import-products.php';
require_once 'inc/class-import-product-categories.php';
require_once 'inc/class-menu-settings.php';
require_once 'inc/class-menu-tool.php';
require_once 'inc/class-import-product-images.php';


/**
* Helper function for get data from moysklad.ru
*/
function wooms_get_data_by_url($url = ''){

	if(empty($url)){
		return false;
	}

	$args = array(
			'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( get_option( 'woomss_login' ) . ':' . get_option( 'woomss_pass' ) )
			)
		);

	$response = wp_remote_get( $url, $args );
	$body = $response['body'];

	return json_decode( $body, true );
}

/**
* Get product id by UUID from metafield
* or false
*/
function wooms_get_product_id_by_uuid($uuid){

	$posts = get_posts('post_type=product&meta_key=wooms_id&meta_value='.$uuid);

	if(empty($posts[0]->ID)){
		return false;
	} else {
		return $posts[0]->ID;
	}
}


/**
* Add Settings link in pligins list
*/
function wooms_plugin_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=mss-settings">Настройки</a>';
		array_push( $links, $settings_link );
		return $links;
}

add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'wooms_plugin_add_settings_link' );

/**
* Log Helper
*/
function wooms_walker_log( $message ) {

	set_transient(
		'wooms_walker_log',
		sprintf(
			"%s\n\n---\n\n%s",
			get_transient( 'wooms_walker_log' ),
			(string) $message
		)
	);

}
