<?php
// Clone field

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$cloned_items = $this->settings['clone']; // get cloned field(s) or field group(s)
$cloned_item_type = ''; // initialise

if ( !empty( $cloned_items) ) { // make sure at least one field has been selected to be cloned

    foreach ($cloned_items as $cloned_item => $cloned_item_slug) {

        $cloned_item_type = substr( $cloned_item_slug, 0, 5);

        // Cloned field
        if ( 'field' === $cloned_item_type ) {

            // Get cloned field

            // Old code:
            // global $wpdb;
            // $single_field_object = $wpdb->get_row( "SELECT * FROM $wpdb->posts WHERE post_name = '$cloned_item_slug' AND post_type = 'acf-field'" );

            $cloned_field_query = new WP_Query( array( 'name' => $cloned_item_slug,
            'post_type' => 'acf-field' ));
            $single_field_object = $cloned_field_query->post;

            if ( $single_field_object ) {

                $args = array(
                    'nesting_level' => $this->nesting_level,
                    'indent_count' => $this->indent_count,
                    'location_val' => $this->location,
                    'field_data_obj' => $single_field_object,
                    'clone_parent_acftcp_field' => $this,
                    'exclude_html_wrappers' => $this->exclude_html_wrappers
                );

                $acftc_field = new ACFTCP_Field( $args );

                echo $acftc_field->get_field_html();

            }

        }
        // Cloned field group
        elseif ( 'group' === $cloned_item_type ) {

            // Get cloned field group froms posts table
            $cloned_field_group_post_object = get_page_by_path( $cloned_item_slug, 'OBJECT', 'acf-field-group' );

            if ( $cloned_field_group_post_object ) {

                $args = array(
                    'field_group_id' => $cloned_field_group_post_object->ID,
                    'nesting_level' => $this->nesting_level,
                    'indent_count' => $this->indent_count,
                    'location' => $this->location,
                    'clone_parent_acftcp_group' => $this,
                    'exclude_html_wrappers' => $this->exclude_html_wrappers
                );
                $cloned_acftcp_group = new ACFTCP_Group( $args );

                echo $cloned_acftcp_group->get_field_group_html();

            }

        }

        $cloned_item_type = ''; // reset

    }

} else { // no fields selected inside clone field

    echo $this->indent . htmlspecialchars("<?php // No fields selected inside clone field ?>")."\n";

}
