<?php

namespace WPSD\remove_nsl_avatars;

defined( 'ABSPATH' ) || die;

add_action('init', __NAMESPACE__ . '\main');

if( ($_GET['page']??'') === 'wpsd-remove-nsl' ) {

	add_action('admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_htmx');
	
}



function main() {
	add_action(
		'admin_menu',
		__NAMESPACE__ . '\add_submenu'
	);
}

function add_submenu() {
	add_submenu_page(
		'upload.php',
		'Remove NSL avatars',
		'Remove NSL avatars',
		'administrator',
		'wpsd-remove-nsl',
		__NAMESPACE__ . '\submenu_page_callback'
	);
}

function submenu_page_callback(){

	require_once WPSD_REMOVE_NSL_DIR . 'class.menu-components.php';

	$title = __('Remove "Nextend Social Login and Register" avatars','wpsd-remove-nsl');

	$text1 = __('All images of users\' avatars that been created by plugin "Nextend Social Login and Register" will be removed.','wpsd-remove-nsl');

	$text2 = __('Recommended make a backup first and test on local WP.','wpsd-remove-nsl');

	$component = new Looper_component();

	$loop_markup = $component->get_loop_markup(); 

	echo <<<HTML
	<div class="wrap">
		<h1>$title</h1>
		<ul style="list-style-type: disc;">
			<li style="margin-left:15px">{$text1}</li>
			<li style="margin-left:15px">{$text2}</li>
		</ul>
		<div>
			<br>
			{$loop_markup}
		</div>
	</div>
	HTML;

}

function enqueue_htmx() {
    $script_url = plugins_url('assets/htmx.min.js', __FILE__);

    wp_enqueue_script(
        'htmx',
        $script_url,
        array(),
        '2.0.1',
        true
    );
}

