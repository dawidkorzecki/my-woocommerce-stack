<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ACFTCP_Location_Registration extends ACF_Admin_Tool {

	public $name = 'acftcp_location_registration';
	public $title = 'Register ACF Blocks or Options Pages';

	/**
	 * This function will output the metabox HTML
	 **/
	function html() {
		include 'location-registration-html.php';
	}

}
