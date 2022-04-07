<?php
/**
 * System panel
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bloginfo = get_bloginfo( 'version' );

$environments = [
	[
		'label' => __( 'WordPress Environment', 'wp-grid-builder' ),
		'data'  => [
			[
				'label'  => __( 'WP Version', 'wp-grid-builder' ),
				'status' => 'v' . $bloginfo,
				'state'  => version_compare( $bloginfo, WPGB_MIN_WP, '>=' ) ? 'true' : 'false',
				'info'   => __( 'The version of WordPress installed on your site.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'WP Active Plugins', 'wp-grid-builder' ),
				'status' => count( Helpers::get_active_plugins() ),
				'state'  => 'none',
				'info'   => __( 'The number of plugins currently activated on your site.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'WP Debug Mode', 'wp-grid-builder' ),
				'status' => Helpers::get_debug_mode() ? __( 'Yes', 'wp-grid-builder' ) : __( 'No', 'wp-grid-builder' ),
				'state'  => Helpers::get_debug_mode() ? 'true' : 'false',
				'info'   => __( 'When activated, PHP errors, notices and warnings will be displayed on your site.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'WP Memory Usage', 'wp-grid-builder' ),
				'status' => Helpers::get_memory_usage() . ' / ' . Helpers::get_memory_limit(),
				'state'  => 'none',
				'info'   => __( 'Amount of PHP memory currently used in your admin dashboard.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'WP Max Upload Size', 'wp-grid-builder' ),
				'status' => Helpers::get_max_upload_size(),
				'state'  => 'none',
				'info'   => __( 'The largest file size that can be uploaded to your WordPress installation.', 'wp-grid-builder' ),
			],
		],
	],
	[
		'label' => __( 'Server Environment', 'wp-grid-builder' ),
		'data'  => [
			[
				'label'  => __( 'Server Info', 'wp-grid-builder' ),
				'status' => Helpers::get_server_software(),
				'state'  => 'none',
				'info'   => __( 'Information about the web server that is currently hosting your site.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'PHP version', 'wp-grid-builder' ),
				'status' => 'v' . PHP_VERSION,
				'state'  => version_compare( PHP_VERSION, WPGB_MIN_PHP, '>=' ) ? 'true' : 'false',
				'info'   => __( 'The version of PHP installed on your hosting server.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'PHP Post Max Size', 'wp-grid-builder' ),
				'status' => Helpers::get_post_max_size(),
				'state'  => 'none',
				'info'   => __( 'Maximum size of post data that can be contained in a page.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'PHP Execution Time', 'wp-grid-builder' ),
				'status' => Helpers::get_max_execution_time(),
				'state'  => 'none',
				'info'   => __( 'Maximum time in seconds a script is allowed to run before it is terminated by the parser.', 'wp-grid-builder' ),
			],
			[
				'label'  => __( 'PHP Max Input Vars', 'wp-grid-builder' ),
				'status' => Helpers::get_max_input_vars(),
				'state'  => 'none',
				'info'   => __( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'wp-grid-builder' ),
			],
		],
	],
];

?>
<div class="wpgb-admin-section">

	<h2><?php esc_html_e( 'System Status', 'wp-grid-builder' ); ?></h2>

	<p>
		<?php esc_html_e( 'The System Status report can be useful for troubleshooting issues with your site.', 'wp-grid-builder' ); ?>
		<br>
		<?php
		printf(
			/* translators: %1$s: Plugin name, %2$s: Minium required WordPress version, %3$s Minium required PHP version */
			esc_html__( '%1$s requires WordPress %2$s and PHP %3$s at minium.', 'wp-grid-builder' ),
			esc_html( WPGB_NAME ),
			esc_html( WPGB_MIN_WP ),
			esc_html( WPGB_MIN_PHP )
		);
		?>
	</p>

</div>

<div class="wpgb-admin-columns wpgb-status-table">

<?php
foreach ( $environments as $environment ) {

	?>
	<div class="wpgb-admin-column">

		<h3><?php echo esc_html( $environment['label'] ); ?></h3>

		<ul class="wpgb-list-table-rows">
		<?php

		foreach ( $environment['data'] as $args ) {

			?>
			<li class="wpgb-list-table-row">

				<div class="wpgb-list-table-column"><?php echo esc_html( $args['label'] ); ?></div>

				<div class="wpgb-list-table-column">
					<span role="tooltip" aria-label="<?php echo esc_attr( esc_html( $args['info'] ) ); ?>" data-tooltip data-layout="large">
						<?php Helpers::get_icon( 'info' ); ?>
					</span>
					<span data-state="<?php echo esc_attr( $args['state'] ); ?>"><?php echo esc_html( $args['status'] ); ?></span>
				</div>

			</li>
			<?php

		}

		?>
		</ul>
	</div>
	<?php

}
?>
</div>

<?php
