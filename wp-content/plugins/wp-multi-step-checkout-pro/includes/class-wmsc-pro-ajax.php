<?php
/**
 * WMSC_Pro_AJAX. AJAX Event Handlers.
 *
 * @package WPMultiStepCheckoutPro
 */

defined( 'ABSPATH' ) || exit;


/**
 * WMSC_Pro_AJAX class.
 */
class WMSC_Pro_AJAX {

	/**
	 * Hook the ajax handlers.
	 */
	public static function init() {
		add_action( 'wp_ajax_wpms_checkout_errors', array( __CLASS__, 'wpms_checkout_errors' ) );
		add_action( 'wp_ajax_nopriv_wpms_checkout_errors', array( __CLASS__, 'wpms_checkout_errors' ) );
	}

	/**
	 * Process ajax checkout form.
	 */
	public static function wpms_checkout_errors() {
		check_ajax_referer( 'wmsc_check_errors' );

		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

		$posted_data = WC()->checkout()->get_posted_data();

		$errors = self::validate_posted_data( $posted_data );

		$errors = apply_filters( 'wmsc_custom_validation', $errors, $posted_data );

		wp_send_json( $errors );
	}

	/** Validates the posted checkout data based on field properties.
	 * Last check for conformity with WooCommerce 6.0.0
	 *
	 * @param  array $data An array of posted data.
	 * @return array An array with errors
	 */
	protected static function validate_posted_data( $data ) {
		$errors = array();
		foreach ( WC()->checkout()->get_checkout_fields() as $fieldset_key => $fieldset ) {
			$validate_fieldset = true;
			if ( self::maybe_skip_fieldset( $fieldset_key, $data ) ) {
				$validate_fieldset = false;
			}

			foreach ( $fieldset as $key => $field ) {
				if ( ! isset( $data[ $key ] ) ) {
					continue;
				}
				$required    = ! empty( $field['required'] );
				$format      = array_filter( isset( $field['validate'] ) ? (array) $field['validate'] : array() );
				$field_label = isset( $field['label'] ) ? $field['label'] : '';

				if ( $validate_fieldset &&
					( isset( $field['type'] ) && 'country' === $field['type'] && '' !== $data[ $key ] ) &&
					! WC()->countries->country_exists( $data[ $key ] ) ) {
						/* translators: ISO 3166-1 alpha-2 country code */
						$errors[ $key ] = sprintf( __( "'%s' is not a valid country code.", 'woocommerce' ), $data[ $key ] );
				}

				switch ( $fieldset_key ) {
					case 'shipping':
						/* translators: %s: field name */
						$field_label = sprintf( _x( 'Shipping %s', 'checkout-validation', 'woocommerce' ), $field_label );
						break;
					case 'billing':
						/* translators: %s: field name */
						$field_label = sprintf( _x( 'Billing %s', 'checkout-validation', 'woocommerce' ), $field_label );
						break;
				}

				if ( in_array( 'postcode', $format, true ) ) {
					$country      = isset( $data[ $fieldset_key . '_country' ] ) ? $data[ $fieldset_key . '_country' ] : WC()->customer->{"get_{$fieldset_key}_country"}();
					$data[ $key ] = wc_format_postcode( $data[ $key ], $country );

					if ( $validate_fieldset && '' !== $data[ $key ] && ! WC_Validation::is_postcode( $data[ $key ], $country ) ) {
						switch ( $country ) {
							case 'IE':
								/* translators: %1$s: field name, %2$s finder.eircode.ie URL */
								$postcode_validation_notice = sprintf( __( '%1$s is not valid. You can look up the correct Eircode <a target="_blank" href="%2$s">here</a>.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>', 'https://finder.eircode.ie' );
								break;
							default:
								/* translators: %s: field name */
								$postcode_validation_notice = sprintf( __( '%s is not a valid postcode / ZIP.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' );
						}
						$errors[ $key ] = apply_filters( 'woocommerce_checkout_postcode_validation_notice', $postcode_validation_notice, $country, $data[ $key ] );
					}
				}

				if ( in_array( 'phone', $format, true ) ) {
					if ( $validate_fieldset && '' !== $data[ $key ] && ! WC_Validation::is_phone( $data[ $key ] ) ) {
						/* translators: %s: phone number */
						$errors[ $key ] = sprintf( __( '%s is not a valid phone number.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' );
					}
				}

				if ( in_array( 'email', $format, true ) && '' !== $data[ $key ] ) {
					$email_is_valid = is_email( $data[ $key ] );
					$data[ $key ]   = sanitize_email( $data[ $key ] );

					if ( $validate_fieldset && ! $email_is_valid ) {
						/* translators: %s: email address */
						$errors[ $key ] = sprintf( __( '%s is not a valid email address.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' );
						continue;
					}
				}

				if ( '' !== $data[ $key ] && in_array( 'state', $format, true ) ) {
					$country      = isset( $data[ $fieldset_key . '_country' ] ) ? $data[ $fieldset_key . '_country' ] : WC()->customer->{"get_{$fieldset_key}_country"}();
					$valid_states = WC()->countries->get_states( $country );

					if ( ! empty( $valid_states ) && is_array( $valid_states ) && count( $valid_states ) > 0 ) {
						$valid_state_values = array_map( 'wc_strtoupper', array_flip( array_map( 'wc_strtoupper', $valid_states ) ) );
						$data[ $key ]       = wc_strtoupper( $data[ $key ] );

						if ( isset( $valid_state_values[ $data[ $key ] ] ) ) {
							// With this part we consider state value to be valid as well, convert it to the state key for the valid_states check below.
							$data[ $key ] = $valid_state_values[ $data[ $key ] ];
						}

						if ( $validate_fieldset && ! in_array( $data[ $key ], $valid_state_values, true ) ) {
							/* translators: 1: state field 2: valid states */
							$errors[ $key ] = sprintf( __( '%1$s is not valid. Please enter one of the following: %2$s', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>', implode( ', ', $valid_states ) );
						}
					}
				}

				if ( $validate_fieldset && $required && '' === $data[ $key ] ) {
					/* translators: %s: field name */
					$errors[ $key ] = apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ), $field_label );
				}
			}
		}
		return $errors;
	}

	/**
	 * See if a fieldset should be skipped.
	 *
	 * @param string $fieldset_key Fieldset key.
	 * @param array  $data Posted data.
	 * @return bool
	 */
	public static function maybe_skip_fieldset( $fieldset_key, $data ) {
		if ( 'shipping' === $fieldset_key && ( ! $data['ship_to_different_address'] || ! WC()->cart->needs_shipping_address() ) ) {
			return true;
		}

		if ( 'account' === $fieldset_key && ( is_user_logged_in() || ( ! WC()->checkout->is_registration_required() && empty( $data['createaccount'] ) ) ) ) {
			return true;
		}

		return false;
	}
}

WMSC_Pro_AJAX::init();
