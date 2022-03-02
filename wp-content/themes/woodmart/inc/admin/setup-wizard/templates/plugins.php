<?php
/**
 * Plugins template.
 *
 * @package woodmart
 */

use XTS\Install_Plugins;

$install_plugins = Install_Plugins::get_instance();
$plugins_list    = $install_plugins->get_plugins();
?>
<div class="xts-plugins<?php echo $install_plugins->is_all_activated() ? ' xts-all-active' : ''; ?>">
	<div class="xts-plugin-response"></div>

	<h3>
		<?php esc_html_e( 'Activation Plugins', 'woodmart' ); ?>
	</h3>

	<p>
		<?php esc_html_e( 'Install and activate plugins for you website.', 'woodmart' ); ?>
	</p>

	<ul>
		<?php foreach ( $plugins_list as $slug => $plugin_data ) : ?>
			<li class="xts-plugin-wrapper">
				<div class="xts-plugin-heading">
					<div class="xts-plugin-img">
						<img src="<?php echo esc_url( $this->get_image_url( $slug . '.svg' ) ); ?>" alt="plugin logo">
					</div>

					<span class="xts-plugin-name">
						<?php echo esc_html( $plugin_data['name'] ); ?>
					</span>
				</div>

				<span class="xts-plugin-required">
					<span>
						<?php if ( $plugin_data['required'] || 'elementor' === $slug || 'js_composer' === $slug ) : ?>
							<?php esc_html_e( 'Required', 'woodmart' ); ?>
						<?php endif; ?>
					</span>
				</span>

				<span class="xts-plugin-version">
					<span>
						<?php echo esc_html( $plugin_data['version'] ); ?>
					</span>
				</span>

				<div class="xts-plugin-btn-wrapper">
					<?php if ( is_multisite() && is_plugin_active_for_network( $plugin_data['file_path'] ) ) : ?>
						<span class="xts-plugin-btn-text">
							<?php esc_html_e( 'Plugin activated globally.', 'woodmart' ); ?>
						</span>
					<?php elseif ( 'require_update' !== $plugin_data['status'] ) : ?>
						<a class="xts-inline-btn xts-btn-underline xts-ajax-plugin xts-<?php echo esc_html( $plugin_data['status'] ); ?>-now"
							href="<?php echo esc_url( $install_plugins->get_action_url( $slug, $plugin_data['status'] ) ); ?>"
							data-plugin="<?php echo esc_attr( $slug ); ?>"
							data-builder="<?php echo isset( $_GET['wd_builder'] ) ? $_GET['wd_builder'] : 'elementor'; ?>"
							data-action="<?php echo esc_attr( $plugin_data['status'] ); ?>">
							<span><?php echo esc_html( $install_plugins->get_action_text( $plugin_data['status'] ) ); ?></span>
						</a>
					<?php else : ?>
						<span class="xts-plugin-btn-text">
							<?php esc_html_e( 'Required update not available', 'woodmart' ); ?>
						</span>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>

	<script>
		var xtsPluginsData = <?php echo wp_json_encode( $plugins_list ); ?>
	</script>
</div>

<div class="xts-wizard-footer">
	<?php $this->get_prev_button( 'page-builder' ); ?>
	<div>
		<a class="xts-inline-btn xts-btn-underline xts-wizard-all-plugins" href="#">
			<?php esc_html_e( 'Install & activate all', 'woodmart' ); ?>
		</a>
		<?php $this->get_next_button( 'dummy-content', '', count( $install_plugins->get_required_plugins_to_activate() ) > 0 ); ?>
	</div>
</div>