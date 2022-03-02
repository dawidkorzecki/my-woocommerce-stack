<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Options;

Options::add_field(
	array(
		'id'          => 'insta_token',
		'name'        => esc_html__( 'Connect instagram account', 'woodmart' ),
		'group'       => esc_html__( 'Instagram API', 'woodmart' ),
		'description' => 'To get this data, follow the instructions in our documentation <a href="https://xtemos.com/docs/woodmart/faq-guides/setup-instagram-api/" target="_blank">here</a>.',
		'type'        => 'instagram_api',
		'section'     => 'api_integrations_section',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'google_map_api_key',
		'name'        => esc_html__( 'Google map API key', 'woodmart' ),
		'group'       => esc_html__( 'Google map API', 'woodmart' ),
		'type'        => 'text_input',
		'description' => wp_kses(
			__( 'Obtain API key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a> to use our Google Map VC element.', 'woodmart' ),
			'default'
		),
		'section'     => 'api_integrations_section',
		'tags'        => 'google api key',
		'priority'    => 20,
	)
);
