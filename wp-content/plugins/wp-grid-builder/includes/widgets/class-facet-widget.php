<?php
/**
 * Facet Widget
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Widgets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Facet Widget
 *
 * @class WP_Grid_Builder\Includes\Widgets\Facet_Widget
 * @since 1.0.0
 */
final class Facet_Widget extends \WP_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct(
			WPGB_SLUG . '_facet',
			WPGB_NAME . ' - ' . __( 'Facet', 'wp-grid-builder' ),
			[ 'description' => esc_html__( 'Displays a facet.', 'wp-grid-builder' ) ]
		);

	}

	/**
	 * Front-end display of widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		if ( empty( $instance['id'] ) || empty( $instance['grid'] ) ) {
			return;
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo wp_kses_post( $args['before_widget'] );

		if ( $title ) {

			echo wp_kses_post( $args['before_title'] );
			echo esc_html( $title );
			echo wp_kses_post( $args['after_title'] );

		}

		wpgb_render_facet( $instance );
		echo wp_kses_post( $args['after_widget'] );

	}

	/**
	 * Back-end widget form.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$facet     = isset( $instance['id'] ) ? $instance['id'] : '';
		$grid      = isset( $instance['grid'] ) ? $instance['grid'] : '';
		$title     = isset( $instance['title'] ) ? $instance['title'] : '';
		$grids     = Widget::get( 'grids' );
		$facets    = Widget::get( 'facets' );
		$templates = Widget::get( 'templates' );

		if ( empty( $facets ) ) {

			$this->button();
			return;

		}

		printf(
			'<p>
				<label for="%1$s">%2$s</label>
				<input id="%1$s" class="widefat" name="%3$s" value="%4$s">
			</p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_html__( 'Title:', 'wp-grid-builder' ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $title )
		);

		Widget::output_list(
			__( 'Facet:', 'wp-grid-builder' ),
			$this->get_field_id( 'id' ),
			$this->get_field_name( 'id' ),
			$facets,
			$facet
		);

		Widget::output_list(
			__( 'Grid to filter:', 'wp-grid-builder' ),
			$this->get_field_id( 'grid' ),
			$this->get_field_name( 'grid' ),
			array_merge( $grids, $templates ),
			$grid
		);

	}

	/**
	 * Output edit button
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function button() {

		$query_args = [
			'page'   => WPGB_SLUG . '-facet-settings',
			'create' => 'true',
		];

		$url = add_query_arg( $query_args, admin_url( 'admin.php' ) );

		echo '<p>';
			echo '<h2>';
				esc_html_e( 'You don\'t have any facet yet!', 'wp-grid-builder' );
			echo '</h2>';
			echo '<a class="button button-primary" href="' . esc_url( $url ) . '" target="_blank">';
				esc_html_e( 'Create a Facet', 'wp-grid-builder' );
			echo '</a>';
		echo '</p>';

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$templates   = apply_filters( 'wp_grid_builder/templates', [] );
		$is_template = isset( $new_instance['grid'], $templates[ $new_instance['grid'] ] );

		return [
			'title' => isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '',
			'grid'  => isset( $new_instance['grid'] ) ? ( $is_template ? $new_instance['grid'] : absint( $new_instance['grid'] ) ) : '',
			'id'    => isset( $new_instance['id'] ) ? absint( $new_instance['id'] ) : '',
		];

	}
}
