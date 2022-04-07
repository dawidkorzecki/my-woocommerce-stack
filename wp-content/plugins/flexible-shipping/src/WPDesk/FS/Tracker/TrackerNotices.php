<?php
/**
 * Class TrackerNotices
 *
 * @package WPDesk\FS\Tracker
 */

namespace WPDesk\FS\Tracker;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Sets WPDesk tracker notices.
 *
 * @package WPDesk\FS\ConditionalMethods\Tracker
 */
class TrackerNotices implements Hookable {

	const USAGE_DATA_URL = 'https://flexibleshipping.com/usage-tracking/';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_notice_content', array( $this, 'tracker_notice' ), 10, 3 );
	}

	/**
	 * Tracker notice content.
	 *
	 * @param string $notice .
	 * @param string $username .
	 * @param string $terms_url .
	 *
	 * @return string
	 */
	public function tracker_notice( $notice, $username, $terms_url ) {
		ob_start();
		?>
		<?php
			// Translators: username.
			echo esc_html( sprintf( __( 'Hey %s,', 'flexible-shipping' ), $username ) );
		?>
		<br/>
		<?php
		echo wp_kses_post(
			sprintf(
				// Translators: strong tag.
				__( 'We are constantly doing our best to %1$simprove our plugins%2$s. That’s why we kindly ask for %1$syour help%2$s to make them even more useful not only for you but also for other %1$s100.000+ users%2$s. Collecting the data on how you use our plugins will allow us to set the right direction for the further development. You can stay asured that no sensitive data will be collected. Can we count on you?', 'flexible-shipping' ),
				'<strong>',
				'</strong>'
			)
		);
		?>
		<a href="<?php echo esc_url( self::USAGE_DATA_URL ); ?>" target="_blank"><?php echo wp_kses_post( __( 'Learn more »', 'flexible-shipping' ) ); ?></a><br/><br/>
		<?php echo wp_kses_post( sprintf( __( 'Thank you in advance!%1$s~ Flexible Shipping Team', 'flexible-shipping' ), '<br/>' ) ); // phpcs:ignore. ?>
		<?php
		$out = ob_get_clean();
		return $out ? $out : '';
	}
}
