<?php
/**
 * Class MatchedShippingZonesNotice
 *
 * @package WPDesk\FS\TableRate\Debug
 */

namespace WPDesk\FS\TableRate\Debug;

use FSVendor\WPDesk\Notice\Notice;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can display notice if multiple shipping zones covers same territory.
 */
class MultipleShippingZonesMatchedSameTerritoryNotice implements Hookable {

	/**
	 * @var \WC_Countries
	 */
	private $countries;

	/**
	 * @var \WC_Shipping_Zones
	 */
	private $shipping_zones;

	/**
	 * MatchedShippingZonesNotice constructor.
	 *
	 * @param \WC_Countries      $countries .
	 * @param \WC_Shipping_Zones $shipping_zones .
	 */
	public function __construct( \WC_Countries $countries, \WC_Shipping_Zones $shipping_zones ) {
		$this->countries      = $countries;
		$this->shipping_zones = $shipping_zones;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_notices', array( $this, 'add_notice_when_multiple_zones_matches_same_territory' ) );
	}

	/**
	 * .
	 *
	 * @return bool|Notice
	 * @internal
	 */
	public function add_notice_when_multiple_zones_matches_same_territory() {
		if ( $this->should_display_notice() ) {
			$shipping_zones_dependencies = $this->prepare_shipping_zones_dependencies();
			if ( count( $shipping_zones_dependencies ) ) {
				$zones_message = $this->prepare_zone_messages( $shipping_zones_dependencies );
				$notice_text = sprintf(
					// Translators: zones.
					__( '%1$sFlexible Shipping hints%2$sA potential shipping zone configuration conflict has been detected: %3$s In order to fix it, change the shipping zones order starting from the narrowest at the very top of the list to the widest at the bottom and refresh the page.', 'flexible-shipping' ),
					'<h4>',
					'</h4>',
					$zones_message
				);
				$notice = new Notice(
					$notice_text,
					Notice::NOTICE_TYPE_ERROR,
					false,
					10,
					array( 'class' => 'flexible-shipping-hint' )
				);

				$matched_shipping_zones_notice = $this;
				/**
				 * Do action after multiple zones matches same territory notice created.
				 *
				 * @param MultipleShippingZonesMatchedSameTerritoryNotice $matched_shipping_zones_notice .
				 */
				do_action( 'flexible-shipping/notice/multiple-zone-matches-same-territory', $matched_shipping_zones_notice );

				return $notice;
			}
		}

		return false;
	}

	/**
	 * .
	 *
	 * @return bool
	 */
	private function should_display_notice() {
		return isset( $_GET['page'] ) && sanitize_key( $_GET['page'] ) === 'wc-settings'
			&& isset( $_GET['tab'] ) && sanitize_key( $_GET['tab'] ) === 'shipping'
			&& empty( $_GET['section'] ) && empty( $_GET['zone_id'] );
	}

	/**
	 * @param array $dependencies .
	 *
	 * @return string
	 */
	private function prepare_zone_messages( $dependencies ) {
		$messages = '';
		foreach ( $dependencies as $dependency ) {
			if ( '' === $messages ) {
				$messages .= '<ul style="list-style-type: disc;">';
			}
			$zones = '';
			/** @var \WC_Shipping_Zone $zone */
			foreach ( $dependency['covered'] as $zone ) {
				$zones .= $zone->get_zone_name() . ', ';
			}
			$zones = trim( $zones, ', ' );
			$messages .= sprintf(
				// Translators: zone messages.
				__( '%1$sWider %2$s shipping zone covers the range of the narrower one placed below: %3$s.%4$s', 'flexible-shipping' ),
				'<li style="margin-left: 30px;">',
				'<strong>' . $dependency['zone']->get_zone_name() . '</strong>',
				'<strong>' . $zones . '</strong>',
				'</li>'
			);
		}
		if ( '' !== $messages ) {
			$messages .= '</ul>';
		}

		return $messages;
	}

	/**
	 * .
	 *
	 * @return array
	 */
	private function prepare_shipping_zones_dependencies() {
		$dependencies = array();
		$zones = $this->get_zones();
		foreach ( $zones as $zone_id => $zone_data ) {
			unset( $zones[ $zone_id ] );
			$zone = $this->get_zone( $zone_id );
			if ( ! $this->zone_contains_postcodes( $zone ) ) {
				$zone_dependencies = $this->prepare_covered_zones( $zone, $zones );
				if ( $zone_dependencies ) {
					$dependencies[ $zone_id ] = array(
						'zone' => $zone,
						'covered' => $zone_dependencies,
					);
				}
			}
		}

		return $dependencies;
	}

	/**
	 * @return array
	 *
	 * @codeCoverageIgnore
	 */
	protected function get_zones() {
		return $this->shipping_zones::get_zones();
	}

	/**
	 * @param int $zone_id .
	 *
	 * @return bool|\WC_Shipping_Zone
	 *
	 * @codeCoverageIgnore
	 */
	protected function get_zone( $zone_id ) {
		return $this->shipping_zones::get_zone( $zone_id );
	}

	/**
	 * @param \WC_Shipping_Zone $zone .
	 * @param array             $zones .
	 *
	 * @return array
	 */
	private function prepare_covered_zones( \WC_Shipping_Zone $zone, array $zones ) {
		$zone_dependencies = array();
		$locations = $zone->get_zone_locations();
		foreach ( $locations as $location ) {
			foreach ( $zones as $zone_id => $zone_data ) {
				$zone = $this->get_zone( $zone_id );
				if ( $this->location_covers_zone( $location, $zone ) ) {
					$zone_dependencies[] = $zone;
				}
			}
		}

		return $zone_dependencies;
	}

	/**
	 * @param \WC_Shipping_Zone $zone .
	 *
	 * @return bool
	 */
	private function zone_contains_postcodes( \WC_Shipping_Zone $zone ) {
		foreach ( $zone->get_zone_locations() as $location ) {
			if ( 'postcode' === $location->type ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \stdClass         $location .
	 * @param \WC_Shipping_Zone $zone .
	 *
	 * @return bool
	 */
	private function location_covers_zone( \stdClass $location, \WC_Shipping_Zone $zone ) {
		$covers = false;
		foreach ( $zone->get_zone_locations() as $zone_location ) {
			$covers = $covers || $this->location_covers_location( $location, $zone_location );
		}

		return $covers;
	}

	/**
	 * @param \stdClass $location .
	 * @param \stdClass $zone_location .
	 *
	 * @return bool
	 */
	private function location_covers_location( \stdClass $location, \stdClass $zone_location ) {
		$zone_continent = '';
		$zone_country   = '';
		$zone_state     = '';
		if ( 'continent' === $zone_location->type ) {
			$zone_continent = $zone_location->code;
		}
		if ( 'country' === $zone_location->type ) {
			$zone_country   = $zone_location->code;
			$zone_continent = $this->get_continent_code_for_country( $zone_country );
		}
		if ( 'state' === $zone_location->type ) {
			$country_state          = $zone_location->code;
			$country_state_exploded = explode( ':', $country_state );
			$zone_country           = $country_state_exploded[0];
			$zone_state             = $country_state_exploded[1];
			$zone_continent         = $this->get_continent_code_for_country( $zone_country );
		}
		if ( 'continent' === $location->type && $location->code === $zone_continent ) {
			return true;
		}
		if ( 'country' === $location->type && $location->code === $zone_country ) {
			return true;
		}
		if ( 'state' === $location->type && $location->code === $zone_state ) {
			return true;
		}
	}

	/**
	 * @param string $country_code .
	 *
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	protected function get_continent_code_for_country( $country_code ) {
		return $this->countries->get_continent_code_for_country( $country_code );
	}

}
