<?php

namespace WPSD\remove_nsl_avatars;

defined( 'ABSPATH' ) || die;

require WPSD_REMOVE_NSL_DIR.'action/remove-nsl-avatars.php';

function get_infinite_loop_response($post){

	$args = get_loop_execution( $post );

	if( $args['continue'] === false ){

		$nsl_dir_path =  wp_upload_dir( null, false )['basedir'].'/nsl_avatars/';

		remove_nsl_folder($nsl_dir_path);
	}

	$output = get_infinite_loop_response_component( $args );

	return $output;

}

function remove_nsl_folder( $dir_path ){

	if( !is_dir($dir_path) ){

		return;
	}

	$dir = dir($dir_path);

	while( ( $item = $dir->read() ) !== false ){
		
		if( $item === '.' || $item === '..' ) continue;
		
		$item_path = "$dir_path/$item";
		
		if( is_dir( $item_path ) ){

			remove_nsl_folder( $item_path );

		}else{

			unlink( $item_path );

		}

	}

	$dir->close();

	rmdir($dir_path);
}


/**
 * Execute your loop code here
 *
 * @param array $post - Input data containing feedback value.
 *
 * @return array -
 * - (mixed)	feedback
 * - (string)	message
 * - (bool)		continue
*/

function get_loop_execution( $post ){
	
	$last_id = $post['feedback']??0;

	$avatar_ids = get_nsl_avatar_ids( $last_id );

	foreach($avatar_ids as $avatar_id){

		remove_nsl_avatar( $avatar_id );
	}

	$count_ids = count($avatar_ids);

	$continue = $count_ids === WPSD_REMOVE_NSL_BATCH_SIZE;

	$message = get_response_message( $count_ids, $continue, $avatar_ids );

	return [

		'feedback'	=> $avatar_id,

		'message'	=> $message,

		'continue'	=> $continue
	];
}

function get_response_message( $count_ids, $continue, $avatar_ids ){

	if( $count_ids === 0){

		return __('No NSL avatars found','wpsd-remove-nsl');
	}

	$message = "{$avatar_ids[0]} -> ".end( $avatar_ids );

	if( !$continue ){
	
		$message .= '<br>'.__('Finished','wpsd-remove-nsl').'</br>';
	}

	return $message;
}

function get_infinite_loop_response_component( $args ){
	
	if( $args['continue'] ){
		
		require_once WPSD_REMOVE_NSL_DIR . 'class.menu-components.php';
	
		$component = new Looper_component();

		$output  = $component->get_continue_loop_component( $args );

	} else {

		$output = $args['message'];

	}

	return $output;
}