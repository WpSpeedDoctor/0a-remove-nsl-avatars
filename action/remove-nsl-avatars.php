<?php

namespace WPSD\remove_nsl_avatars;

defined( 'ABSPATH' ) || die;

define( 'NSL_DIR', wp_upload_dir( null, false )['basedir'].'/nsl_avatars/' );

function remove_nsl_avatar( $avatar_post_id ){

	$nsl_files = get_nsl_files( $avatar_post_id );

	remove_nsl_files( $nsl_files );

	wp_delete_post( $avatar_post_id, true );

}

function get_nsl_avatar_ids( $last_id ){

	global $wpdb;

	$is_source_postmeta = false;

	$batch_size = WPSD_REMOVE_NSL_BATCH_SIZE;

	if( $is_source_postmeta ){

		$avatar_ids = $wpdb->get_col($wpdb->prepare(
			<<<SQL
			SELECT post_id 
			FROM {$wpdb->postmeta} 
			WHERE meta_key = '_wp_attached_file' 
			AND meta_value LIKE 'nsl_avatars/%' 
			AND post_id > %d 
			ORDER BY post_id ASC 
			LIMIT {$batch_size}
			SQL
			, $last_id));

	} else {
		
		$avatar_ids = $wpdb->get_col($wpdb->prepare(
			<<<SQL
			SELECT ID 
			FROM {$wpdb->posts} 
			WHERE guid LIKE '%/nsl_avatars/%' 
			LIMIT {$batch_size}
			SQL
			, $last_id));

	}

	return empty($avatar_ids) ? [] : $avatar_ids;
}


function remove_nsl_files( $nsl_files ){

	if( empty( $nsl_files) ){
		return;
	}

	foreach( $nsl_files as $filename ){

		$filepath = NSL_DIR.$filename;

		if( !file_exists( $filepath ) ) {
		
			continue;
		}
		
		unlink($filepath);

	}

}

function get_nsl_files( $avatar_post_id ){

	$nsl_files_data = get_nsl_files_data( $avatar_post_id );

	if( empty( $nsl_files_data['file'] ) ) {

		return false;
	}

	$result = [ 
		basename( $nsl_files_data['file'] )
	];

	foreach( $nsl_files_data['sizes'] as $size_name => $size_data){

		$result[]= $size_data['file'];

	}

	return array_unique($result);
}

function get_nsl_files_data( $avatar_post_id ){

	global $wpdb;

	$attachment_data = $wpdb->get_var($wpdb->prepare(
		<<<SQL
		SELECT meta_value 
		FROM {$wpdb->postmeta} 
		WHERE meta_key = '_wp_attachment_metadata' 
		AND post_id = '%d' 
		LIMIT 1
		SQL
		, $avatar_post_id));

	$result = maybe_unserialize( $attachment_data );
	
	return is_array( $result ) ? $result : false;
}
