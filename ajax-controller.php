<?php

namespace WPSD\remove_nsl_avatars;

defined( 'ABSPATH' ) || die;

if( $_POST['ajax_nonce'] !== get_nonce() ){

	http_response_code(401);

	die( __('Wrong nonce','wpsd-remove-nsl') );
}

define( 'WPSD_REMOVE_NSL_BATCH_SIZE', 100 );

$action = str_replace( 'wpsd-remove-nsl-', '', $_POST['action'] );

switch( $action ){

	case 'infinite':

		require WPSD_REMOVE_NSL_DIR.'action/infinite.php';

		$response = get_infinite_loop_response($_POST);
		break;

	default:
	$response = 'No such action';
			break;

}

echo $response;

die;
