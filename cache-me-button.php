<?php
/**
 * Plugin Name:  Swift Performance Cache Me - WP Fix It
 * Description:  Adds a button to the bottom of front-end pages so you can force cache creation of any site page without waiting for it to build manually. ONLY WORKS WITH SWIFT PERFORMANCE caching plugin.
 * Version:      2.2
 * Plugin URI:   #
 * Author:       WP Fix It
 * Author URI:   https://www.wpfixit.com
 * Text Domain:  cache-me-button
 * Domain Path:  /languages/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
add_action( 'admin_bar_menu', array( 'Cache_Me_Button', 'add_button_to_toolbar' ), 5000 );
add_action( 'wp_footer', array( 'Cache_Me_Button', 'add_button_to_footer' ) );
class Cache_Me_Button {
	public static function add_button_to_toolbar( $wp_toolbar ){
		if ( is_super_admin() && is_admin_bar_showing() && ! is_admin() ) {
			$args = array(
				'id' => 'cache-me-button',
				'title' => __( 'CACHE ME!', 'cache-me-button' ),
				'href' => Cache_Me_Button::get_button_url() );
			$wp_toolbar->add_node( $args );
		}
	}
	public static function get_button_url(){
		$new_url = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] === 'on' ? "https" : "http" ) . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
		if ( strpos( $new_url, '?force-cache' ) != false || strpos( $new_url, '&force-cache' ) != false ) {
			return $new_url;
		} else {
			return $new_url . ( strpos( $new_url, '?' ) != false ? '&force-cache' : '?force-cache' );
		}
	}
	//If needed anywhere within the theme, use this
	public static function get_button_html() {
		return '<a class="cache-me button" style="margin-bottom: 20px;font-weight: bold;font-size: 18px;border-radius: 8px;background-color: #d52228;color: #fff;padding: 10px;position: relative;margin-left: 40%;z-index: 99999;display: block;position: fixed;bottom: 0;" href="' . Cache_Me_Button::get_button_url() . '">' . __( 'CACHE ME!', 'cache-me-button' ) .'</a>';
	}
	public static function add_button_to_footer() {
		if ( is_super_admin() && ! is_admin_bar_showing() && ! is_admin()) {
			echo Cache_Me_Button::get_button_html();
		}
	}
}

 /* Activate the plugin and do something. */
function cache_me_plugin_action_links( $links ) {
    echo '<style>span#p-icon{width:23px!important}span#p-icon:before{width:32px!important;font-size:23px!important;color:#3B657D!important;background:0 0!important;box-shadow:none!important}</style>';
$links = array_merge( array(
'<a href="https://www.wpfixit.com/wordpress-speed-optimization-service/" target="_blank">' . __( '<b><span id="p-icon" class="dashicons dashicons-performance"></span> <span style="color:#f99568">GET SPEED</span></b>', 'textdomain' ) . '</a>'
), $links );
return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cache_me_plugin_action_links' );
/* Activate the plugin and do something. */
register_activation_hook( __FILE__, 'cache_me_welcome_message' );
function cache_me_welcome_message() {
set_transient( 'cache_me_welcome_message_notice', true, 5 );
}
add_action( 'admin_notices', 'cache_me_welcome_message_notice' );
function cache_me_welcome_message_notice(){
/* Check transient, if available display notice */
if( get_transient( 'cache_me_welcome_message_notice' ) ){
?>
<div class="updated notice is-dismissible">
	<style>div#message {display: none}</style>
<p>&#127881; <strong>WP Fix It - Swift Performance Cache Me</strong> has been activated and you can now cache any front-end page without waiting.
</div>
<?php
/* Delete transient, only display this notice once. */
delete_transient( 'cache_me_welcome_message_notice' );
}
}
// Check if WooCommerce is active
if ( ! in_array( 'swift-performance/performance.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function cache_me_needed_notice() {
		$message = sprintf(
		/* translators: Placeholders: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
			esc_html__( '%1$sSwift Performance Cache Me %2$s requires Swift Performance to function. Please %3$sinstall Swift Performance%4$s.', 'cache_me' ),
			'<strong>',
			'</strong>',
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'&nbsp;&raquo;</a>'
		);
		echo sprintf( '<div class="error"><p>%s</p></div>', $message );
	}
	add_action( 'admin_notices', 'cache_me_needed_notice' );
	return;
}