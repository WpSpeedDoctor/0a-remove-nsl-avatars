<?php

/*
* Plugin Name: Remove NSL Avatars
* Plugin URI: https://wpspeeddoctor.com/plugins/
* Description: Remove useless avatars images
* Version: 1.1
* Author: Jaro Kurimsky
* Author URI: https://wpspeeddoctor.com/
* Text Domain: wpsd-remove-nsl
* License: GPLv3
* Requires at least: 5.9
* Requires PHP: 7.4.0
*/

namespace WPSD\remove_nsl_avatars;

defined( 'ABSPATH' ) || die;

define('WPSD_REMOVE_NSL_DIR', __DIR__ . '/');

/**
 * Runtime
 */

if( !is_admin() ) {
	return;
}

if( wp_doing_ajax() && !empty($_POST['action']) && str_starts_with($_POST['action'],'wpsd-remove-nsl') ){

	require __DIR__.'/ajax-controller.php';

} else {

	require __DIR__.'/admin.php';

}

/**
 * Essential functions
 */
function get_nonce(){

	return hash('fnv164', (LOGGED_IN_COOKIE.date('j')));
}

function get_ajax_url(){

	return admin_url('/admin-ajax.php?wpsd-remove-nsl');
}

function get_htmx_vals_json( $value ){

	return json_encode( $value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
}