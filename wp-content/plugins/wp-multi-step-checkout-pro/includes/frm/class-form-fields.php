<?php
/**
 * Form_Fields. Render form fields out of an array.
 *
 * @class   Form_Fields
 * @package SilkyPressFrm
 */

namespace SilkyPressFrm;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\SilkyPressFrm\Form_Fields' ) ) {
	/**
	 * Form_Fields class.
	 */
	class Form_Fields {

		/**
		 * Fields description.
		 *
		 * @var array
		 */
		private $fields = array();

		/**
		 * Current field values.
		 *
		 * @var array
		 */
		private $current_values = array();

		/**
		 * Additional attributes.
		 *
		 * @var array
		 */
		private $atts = array();

		/**
		 * Validation error/info messages.
		 *
		 * @var array
		 */
		private $messages = array();

		/**
		 * Constructor.
		 *
		 * @param array $fields The field description array.
		 */
		public function __construct( $fields = array() ) {
			$this->fields = $fields;

			// Default attributes.
			$this->atts = array(
				'tooltip_img' => '',
				'section'     => '',
				'label-class' => '',
				'disable_pro' => true,
			);
		}

		/**
		 * Add settings.
		 *
		 * @param string $var   Variable.
		 * @param string $value Value.
		 */
		public function add_setting( $var = '', $value = '' ) {
			$this->atts[ $var ] = $value;
		}

		/**
		 * Set the $this->current_values array.
		 *
		 * @param array $values Current values.
		 */
		public function set_current_values( $values = array() ) {
			$this->current_values = $values;
		}

		/**
		 * Render all the fields.
		 *
		 * @param array $fields The fields to be rendered.
		 *
		 * @return string The rendered fields.
		 */
		public function render( $fields = null ) {

			$content = '';

			$fields = ( null === $fields ) ? $this->fields : array();

			if ( count( $fields ) === 0 ) {
				return '';
			}

			foreach ( $fields as $_key => $_field ) {
				if ( isset( $this->atts['section'] ) && $_field['section'] !== $this->atts['section'] ) {
					continue;
				}
				$content .= $this->render_field( $_key, $_field );
			}

			return $content;
		}

		/**
		 * Render one field
		 *
		 * @param string $_key The field's id.
		 * @param array  $_field The fields' description.
		 *
		 * @return string The rendered field.
		 */
		public function render_field( $_key, $_field ) {
			$atts         = '';
			$description  = '';
			$label        = '';
			$input_values = '';
			$add_label    = true;
			$group_wrap   = true;

			if ( 'header' === $_field['input_form'] || isset( $_field['no_wrap'] ) ) {
				$add_label  = true;
				$group_wrap = false;
			}

			$_field['value'] = isset( $this->current_values[ $_key ] ) ? $this->current_values[ $_key ] : $_field['value'];

			$_field['disabled'] = ( $this->atts['disable_pro'] && isset( $_field['pro'] ) && $_field['pro'] ) ? true : false;

			$atts .= ( isset( $_field['disabled'] ) && $_field['disabled'] ) ? ' disabled' : '';

			$atts .= ( 'checkbox' === $_field['input_form'] && true === (bool) $_field['value'] && ! $_field['disabled'] ) ? ' checked="checked"' : '';

			// Radio templates.
			if ( 'radio' === $_field['input_form'] ) {
				foreach ( $_field['values'] as $__id => $__value ) {
					$_style        = isset( $_field['style'] ) && 'inline' === $_field['style'] ? '-inline' : '';
					$input_atts    = ( $__id === $_field['value'] ) ? $atts . ' checked=""' : $atts;
					$input_values .= vsprintf(
						'<div class="radio%s"%s><label><input type="radio" name="%s" id="%s" value="%s" %s />%s</label></div>',
						array( $_style, $atts, $_key, $__id, $__id, $input_atts, $__value )
					);
				}
			}

			// Button templates.
			if ( 'buttons' === $_field['input_form'] ) {
				foreach ( $_field['values'] as $__id => $__value ) {
					$toggle  = ( ! empty( $__value[1] ) ) ? ' data-toggle="tooltip" data-placement="top" title="' . $__value[1] . '" data-original-title="' . $__value[1] . '"' : '';
					$__atts  = ( $__id === $_field['value'] ) ? ' active' : '';
					$__atts .= ( $_field['disabled'] ) ? ' disabled' : '';

					$input_values .= vsprintf(
						'<label class="btn btn-default %s"><input type="radio" name="%s" id="%s" value="%s" %s /><div class="icon-in-label ndd-spot-icon icon-style-1"%s><div class="ndd-icon-main-element">%s</div></div></label>',
						array( $__atts, $_key, $__id, $__id, $__id === $_field['value'] ? 'checked' : '', $toggle, $__value[0] )
					);
				}
			}

			// Input templates.
			$templates = array(
				'text'        => array( '%s', array( $_field['value'] ) ),
				'radio'       => array( '%s', array( $input_values ) ),
				'buttons'     => array( '<div class="btn-group%s" data-toggle="buttons" id="btn-group-style-circle">%s</div>', array( $atts, $input_values ) ),
				'input_color' => array(
					'<input type="color" class="form-control" id="%s" name="%s" value="%s"%s /><span class="input-group-addon" id="color-text-color-hex">%s</span>',
					array( $_key, $_key, $_field['value'], $atts, $_field['value'] ),
				),
				'input_text'  => array(
					'<input type="text" class="form-control" id="%s" name="%s" value="%s"%s />',
					array( $_key, $_key, stripslashes( $_field['value'] ), $atts ),
				),
				'checkbox'    => array(
					'<input type="checkbox" id="%s" name="%s" value="1"%s />',
					array( $_key, $_key, $atts ),
				),
				'header'      => array(
					'<h4 class="col-sm-5">%s</h4><div style="clear: both;"></div>',
					array( $_field['label'] ),
				),
			);

			// The input.
			$input = vsprintf(
				$templates[ $_field['input_form'] ][0],
				$templates[ $_field['input_form'] ][1]
			);

			// The input addon.
			if ( isset( $_field['post_input'] ) && ! empty( $_field['post_input'] ) ) {
				$input .= sprintf( '<span class="input-group-addon">%s</span>', $_field['post_input'] );
			}

			// The description.
			if ( isset( $_field['description'] ) && ! empty( $_field['description'] ) ) {
				$description = vsprintf(
					' <img src="%s" data-toggle="tooltip" data-placement="top" title="%s" data-original-title="%s" />',
					array( $this->atts['tooltip_img'], $_field['description'], $_field['description'] )
				);
			}

			// The label.
			$label_class = isset( $this->atts['label_class'] ) ? $this->atts['label_class'] : 'col-sm-6';
			$label_class = isset( $_field['label_class'] ) ? $_field['label_class'] : $label_class;
			if ( isset( $_field['label'] ) && $add_label ) {
				$label = vsprintf(
					'<label for="%s" class="%s">%s</label>',
					array( $_key, 'control-label ' . $label_class, $_field['label'] . $description )
				);
			}

			// The Bootstrap 4 form-group wrapper.
			if ( $group_wrap ) {
				$_field['disabled'] = ( $this->atts['disable_pro'] && isset( $_field['pro'] ) && $_field['pro'] ) ? true : false;
				$class              = ( isset( $_field['disabled'] ) && $_field['disabled'] ) ? ' disabled' : '';
				$input              = vsprintf(
					'<div class="%s"%s>%s<div class="%s">%s</div></div>',
					array( 'form-group' . $class, '', $label, 'input-group input-group-' . $_field['input_form'], $input )
				);
			}

			return $input;
		}

		/**
		 * Validate the $_POST values.
		 *
		 * @param array $post The $_POST values.
		 */
		public function validate( $post ) {

			// Filter the $post array for allowed fields.
			$post = array_intersect_key( $post, array_fill_keys( array_keys( $this->fields ), '' ) );

			foreach ( $this->fields as $_key => $settings ) {
				$reset = array();

				// Validate only fields from the current section.
				if ( isset( $this->atts['section'] ) && $settings['section'] !== $this->atts['section'] ) {
					if ( 'header' !== $settings['input_form'] && 'text' !== $settings['input_form'] && isset( $settings['value'] ) ) {
						$post[ $_key ] = isset( $this->current_values[ $_key ] ) ? $this->current_values[ $_key ] : $settings['value'];
					}
					continue;
				}

				// Add the unchecked checkboxes.
				if ( 'checkbox' === $settings['input_form'] ) {
					$post[ $_key ] = isset( $post[ $_key ] ) ? true : false;
				}

				// Validate colors.
				if ( 'input_color' === $settings['input_form'] && isset( $post[ $_key ] ) && ! preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $post[ $_key ] ) ) {
					$reset = array(
						/* translators: 1: field label 2: value */
						__( 'Unrecognized %1$s. The value was reset to %2$s' ),
						array( '<b>' . $settings['label'] . '</b>', '<b>' . $settings['value'] . '</b>' ),
					);
				}

				// Sanitize text inputs.
				if ( 'input_text' === $settings['input_form'] ) {
					$post[ $_key ] = isset( $post[ $_key ] ) ? filter_var( sanitize_text_field( $post[ $_key ] ), FILTER_SANITIZE_STRING ) : (string) $settings['value'];
				}

				// Validate button and radio inputs.
				if ( in_array( $settings['input_form'], array( 'button', 'radio' ), true ) && isset( $post[ $_key ] ) && ! array_key_exists( $post[ $_key ], $settings['values'] ) ) {
					$reset = array(
						/* translators: 1: field label 2: value */
						__( 'Unrecognized %1$s. The value was reset to %2$s' ),
						array( '<b>' . $settings['label'] . '</b>', '<b>' . $settings['value'] . '</b>' ),
					);
				}

				// Validate according to a rule.
				if ( isset( $settings['validate'] ) && isset( $post[ $_key ] ) ) {
					if ( 'int' === $settings['validate']['type'] ) {
						if ( (string) (int) $post[ $_key ] !== $post[ $_key ] ) {
							$this->add_message(
								'info',
								vsprintf(
									/* translators: 1: field label 2: value */
									__( 'The %1$s field accepts only an integer value. It was set to %2$s' ),
									array( '<b>' . $settings['label'] . '</b>', (int) $post[ $_key ] )
								)
							);
						}
						$post[ $_key ] = (int) $post[ $_key ];
					}
					if ( 'float' === $settings['validate']['type'] ) {
						if ( (string) (float) $post[ $_key ] !== $post[ $_key ] ) {
							$this->add_message(
								'info',
								vsprintf(
									/* translators: 1: field label 2: value */
									__( 'The %1$s field accepts only a number. It was set to %2$s' ),
									array( '<b>' . $settings['label'] . '</b>', (float) $post[ $_key ] )
								)
							);
						}
						$post[ $_key ] = (float) $post[ $_key ];
					}

					if ( in_array( $settings['validate']['type'], array( 'int', 'float' ), true ) &&
							( $post[ $_key ] < $settings['validate']['range'][0] ||
							$post[ $_key ] > $settings['validate']['range'][1] ) ) {
						$reset = array(
							/* translators: 1: field label 2: minimum value 3: maximum value 4: value */
							__( '%1$s accepts values between %2$s and %3$s. Your value was reset to %4$s' ),
							array( '<b>' . $settings['label'] . '</b>', $settings['validate']['range'][0], $settings['validate']['range'][1], '<b>' . $settings['value'] . '</b>' ),
						);
					}
				}

				// Reset the value and add the info message.
				if ( count( $reset ) > 0 ) {
					$post[ $_key ] = $settings['value'];
					$this->add_message( 'info', vsprintf( $reset[0], $reset[1] ) );
				}
			}
			return $post;
		}


		/**
		 * Add messages.
		 *
		 * @param string $type    The message type.
		 * @param string $message The message.
		 */
		public function add_message( $type = '', $message = '' ) {
			$this->messages[] = array( $type, $message );
		}

		/**
		 * Render the messages.
		 *
		 * @return string Messages.
		 */
		public function render_messages() {
			if ( 0 === count( $this->messages ) ) {
				return;
			}

			$output = '';
			foreach ( $this->messages as $_message ) {
				$output .= vsprintf(
					'<div class="alert alert-%s"><button type="button" class="close" data-dismiss="alert">&times;</button>%s</div>',
					array( $_message[0], $_message[1] )
				);
			}

			$output = sprintf( '<div class="col-lg-12">%s</div>', $output );

			return $output;
		}
	}
}
