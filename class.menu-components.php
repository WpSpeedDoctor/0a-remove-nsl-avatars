<?php

namespace WPSD\remove_nsl_avatars;

defined( 'ABSPATH' ) || die;

class Looper_component{
	
	private $nonce;

	private $htmx_url;

	private $htmx_indicator;

	public function __construct(){
    
		$this->nonce = get_nonce();

		$this->htmx_url = get_ajax_url();

		$this->htmx_indicator = $this->get_htmx_indicator_markup();

    }

	private function get_htmx_vals_json( $value ){

		return json_encode( $value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
	}

	private function get_htmx_indicator_markup(){

		$spinner_url = admin_url('/images/spinner.gif');

		return <<<HTML
		<img class="htmx-indicator" id="spinnerContainer" src="{$spinner_url}">
		HTML;
	}

	public function get_loop_markup(){

		$action = 'infinite';
		
		$button_text = __( 'Permanently remove NSL avatars', 'wpsd-remove-nsl' );
		
		$vals= [

			'action' => 'wpsd-remove-nsl-'.$action ,
			'ajax_nonce' => $this->nonce,
		];

		$vals_json = get_htmx_vals_json($vals);
			
		return <<<HTML
		<div id="execute-{$action}">
			<button class="button"
					hx-post="{$this->htmx_url}"
					hx-vals='{$vals_json}'
					hx-target="#execute-{$action}"
					hx-swap="outerHTML"
					hx-indicator="#spinnerContainer"
					>
				{$button_text}
			</button>
			{$this->htmx_indicator}
		</div>
		HTML;
	}

	public function get_continue_loop_component( $args=[] ){

		$action = 'infinite';

		$args['message']??='Empty';

		$vals= [
			'action' => 'wpsd-remove-nsl-'.$action ,
			'ajax_nonce' => $this->nonce,
			'feedback' => $args['feedback']??'',
		];

		$vals_json = get_htmx_vals_json($vals);
		
		return <<<HTML
		<div id="execute-{$action}"
			hx-trigger="load"
			hx-post="{$this->htmx_url}"
			hx-vals='{$vals_json}'
			hx-target="#execute-{$action}"
			hx-swap="outerHTML"
			hx-indicator="#spinnerContainer">
			{$args['message']}
			{$this->htmx_indicator}
		</div>
		HTML;
	}

	public function get_input_value_markup(){

		$action = 'enter-value';

		$vals= [

			'action' => 'wpsd-remove-nsl-'.$action ,
			'ajax_nonce' => $this->nonce,
		];

		$vals_json = get_htmx_vals_json($vals);
		
		return <<<HTML
		<form hx-post="{$this->htmx_url}"
			hx-vals='{$vals_json}'
			hx-target="#execute-{$action}"
			hx-swap="innerHTML"
			hx-indicator="#spinnerContainer">
			<input type="text" id="input1" name="input1" placeholder="Enter first value">
			<br><br>
			<button type="submit">Start</button>
		</form>
		
		<br>
		<div id="execute-{$action}"></div>
		{$this->htmx_indicator}
	HTML;

	}

}