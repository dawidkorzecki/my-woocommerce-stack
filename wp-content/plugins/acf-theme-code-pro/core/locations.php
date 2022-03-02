<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Content of theme code meta box
*/
class ACFTCP_Locations {

	// Data from field group post object
	private $field_group_post_ID = null;

	// Location rules
	private $location_rules = array();

	// Locations that are excluded because they aren't really locations
	// (they relate to the backend visiblity of the field group)
	private static $locations_excluded = array(
		// ACF v5
		'current_user',
		'current_user_role',
		'user_role',
		// ACF v4
		'user_type', // Logged in User Type
		'ef_user' // User
	);

	/**
	 * ACFTCP_Locations constructor
	 *
	 * @param WP_Post $post Post object for ACF field group
	 */
	public function __construct( $field_group_post_obj ) {

		if ( !empty( $field_group_post_obj ) ) {

			// Save field group post ID
			$this->field_group_post_ID = $field_group_post_obj->ID;

			// Save field group location rules
			$this->location_rules = $this->get_location_rules( $field_group_post_obj );

		}

	}

	/**
	* Get field group location rules
	*
	* @param Field group post object
	* @return Array of location rule arrays like this:
	*
	* Array (
	*   [param] => post
	*   [operator] => ==
	*   [value] => 1
	* )
	*/
	private function get_location_rules( $field_group_post_obj ) {

		// ACF v5
		if ( 'posts' == ACFTCP_Core::$db_table ) {
			return $this->get_location_rules_from_posts_table( $field_group_post_obj );
		}

		// ACF v4
		elseif ( 'postmeta' == ACFTCP_Core::$db_table ) {
			return $this->get_location_rules_from_postmeta_table( $field_group_post_obj );
		}

	}


	/**
	 * Get field group location rules from posts table (ACF v5)
	 *
	 * @param Field group post object
	 * @return Array of location rule arrays
	 */
	private function get_location_rules_from_posts_table( $field_group_post_obj ) {

		$location_rules = array();

		// Get location rules from field group post content
		// html entity decode added to fix issue with 'Disable the visual editor when writing'
		$field_group_post_content = maybe_unserialize( html_entity_decode( $field_group_post_obj->post_content ));

		if ( $field_group_post_content ) {
			foreach ( $field_group_post_content['location'] as $location_rule_group ) {

				foreach ( $location_rule_group as $location_rule ) {

					// Only include location rules that are actual locations
					if ( $this->is_included_location_rule( $location_rule ) ) {
						$location_rules[] = $location_rule;
					}
				}
			}
		}

		return $location_rules;

	}


	/**
	* Get all location rules for field group from postmeta table (ACF v4)
	*
	* @param Field group post object
	* @return Array of location rule arrays
	*/
	private function get_location_rules_from_postmeta_table( $field_group_post_obj ) {

		$location_rules = array();

		global $wpdb;

		// Prepend table prefix
		$table = $wpdb->prefix . 'postmeta';

		// Query postmeta table for location rules associated with this field group
		$query_results = $wpdb->get_results( "SELECT * FROM " . $table . " WHERE post_id = " . $field_group_post_obj->ID . " AND meta_key LIKE 'rule'" );

		foreach ( $query_results as $query_result ) {

			// Unserialize location rule data
			$location_rule = unserialize( $query_result->meta_value );

			// If location rule is excluded, skip to next location rule
			if ( ! ($this->is_included_location_rule( $location_rule ) ) ) {
				continue;
			}

			// Change ACF v4 location slugs to match ACF v5
			switch ( $location_rule['param'] ) {
				case 'ef_media':
					$location_rule['param'] = 'attachment';
					break;

				case 'ef_taxonomy':
					$location_rule['param'] = 'taxonomy';
					break;
			}

			// Remove data that is not required (so location rule format matches location rules retrieved from posts table)
			unset( $location_rule['order_no'] );
			unset( $location_rule['group_no'] );

			// Create and array of all location rules
			$location_rules[] = $location_rule;

		}

		return $location_rules;

	}


	/**
	* Exclude location rules that aren't really locations
	* (they relate to the backend visiblity of the field group)
	*
	* @param Array $location_rule
	* @return bool 
	*
	* Requires $this->$locations_excluded
	*
	*/
	private function is_included_location_rule( $location_rule ) {

		return ( ! in_array( $location_rule['param'], self::$locations_excluded ) );

	}


	/**
	 * Get locations HTML
	 * 
	 * @return string
	 */
	public function get_locations_html() {

		$args = array(
			'field_group_id' => $this->field_group_post_ID
			// no location argument included
		);
		$parent_field_group= new ACFTCP_Group( $args );

		// If no fields in field group: display notice
		// (needs to be done at this level because ACFTC Group class is used recursively)
		if ( empty( $parent_field_group->fields ) ) { 
			
			ob_start();?>

			<div class="acftc-intro-notice">
				<p>Create some fields and publish the field group to generate theme code.</p>
			</div>
			
			<?php return ob_get_clean();
		}

		// If all locations are excluded: render fields without location ui
		// elements (eg. only the Current User location is selected)
		if ( empty( $this->location_rules ) ) {
			return $parent_field_group->get_field_group_html();
		}

		ob_start();

		// If more than one location: render location select
		if ( count( $this->location_rules) > 1 ) {
			echo $this->get_location_select_html();
		}

		// Render ALL fields for every location
		foreach ( $this->location_rules as $key => $location_rule ) :

			$args = array(
				'field_group_id' => $this->field_group_post_ID,
				'location' => $location_rule['param'] // included this time
			);
			$parent_field_group = new ACFTCP_Group( $args ); 
			
			// Wrapping div used for show and hide functionality

?>
<div id="acftc-group-<?php echo $key; ?>" class="location-wrap">
<?php 

				echo $this->get_location_helper_html( $location_rule );
				
				if ( $location_rule['param'] != 'block' ) { 
					echo $parent_field_group->get_field_group_html(); 
				}
				
?>
</div>
<?php

		endforeach;

		return ob_get_clean();

	}


	/**
	 * Get location option text for use in location select
	 * 
	 * @param Array $location_rule
	 * Array (
	 *	[param] => block
	 *	[operator] => ==
	 *	[value] => acf/example
	 * ) 
	 * @return string 
	 * 
	 */
	private function get_location_option_text( $location_rule ) { 

		if ( !$location_rule ) {
			return;
		}

		$location_name_title_case = ucwords( str_replace('_', ' ', $location_rule['param'] ) );

		if ( empty( $location_rule['value'] ) ) {
			$location_value_no_dashes = 'unknown';
		} else {
			$location_value_no_dashes = str_replace( '-' , ' ', $location_rule['value'] );
		}
		
		$location_value_clean = str_replace( array( 'category:', 'acf/' ) , '', $location_value_no_dashes );
		$location_value_title_case = ucwords( $location_value_clean );

		$location_operator_text = ($location_rule['operator'] === '==' ) ? '' : 'Not ';

		return "{$location_name_title_case} ({$location_operator_text}{$location_value_title_case})";

	}


	/**
	 * Render header for location select
	 * 
	 * @return string 
	 */
	private function get_location_select_html() { 
		
		ob_start();
		?>
<div class="inside acf-fields -left acf-locations">
	<div class="acf-field acf-field-select" data-name="style" data-type="select">
		<div class="acf-label">
			<label for="acf_field_group-style">Location</label>
		</div>
		<div class="acf-input">
			<select id="acftc-group-option" class="" data-ui="0" data-ajax="0" data-multiple="0" data-placeholder="Select" data-allow_null="0">
<?php foreach ( $this->location_rules as $key => $location_rule ) : ?>
				<option value="acftc-group-<?php echo $key; ?>"><?php echo $this->get_location_option_text( $location_rule ); ?></option>
<?php endforeach; ?>
			</select>
		</div>
	</div>
</div>

<?php 
		return ob_get_clean(); 
	}

	/**
	 * Get html for location helper
	 *
	 * @param Array $location_rule
	 * Array (
	 *	[param] => block
	 *	[operator] => ==
	 *	[value] => acf/example
	 * )
	 * @return string 
	 */
	private function get_location_helper_html( $location_rule ) { 

		$location_helper_title = $this->get_location_helper_title( $location_rule );
		$location_helper_php = $this->get_location_helper_php( $location_rule );
		
		if ( !$location_helper_title || !$location_helper_php ) {
			return "";
		}
		
		ob_start();

?>
	<div class="acftc-field-meta">
		<span class="acftc-field-meta__title" data-pseudo-content="<?php echo $location_helper_title; ?>"></span>
	</div>

	<div class="acftc-field-code">
		<a href="#" class="acftc-field__copy acf-js-tooltip" title="Copy to Clipboard"></a>
		<pre class="line-numbers"><code class="language-php"><?php echo $location_helper_php; ?></code></pre>
	</div>
<?php

		return ob_get_clean();
		
	}

	/**
	 * Get location helper block title
	 * 
	 * @param Array $location_rule
	 * Array (
	 *	[param] => block
	 *	[operator] => ==
	 *	[value] => acf/example
	 * )
	 * @return string/false
     *
	 */
	private function get_location_helper_title( $location_rule ) { 

		if ( empty( $location_rule ) ) {
			return false;
		}

		$location_slug = $location_rule['param'];

        switch ( $location_slug ) {

            case 'user_form':
                return 'User Variables';
                break;
            
            case 'attachment':
                return 'Attachment Variables';
                break;
               
            case 'taxonomy':
                return 'Taxonomy Term Variables';
                break;

            case 'comment':
                return 'Comment Variables';
                break;
                
            case 'widget':
                return 'Widget Variables';
                break;

			case 'block':
				return 'Block Template';
                break;                
                            
			default:
				return false;
				break;
		}

	}

	
	/**
	 * Get location helper php
	 *
	 * @param Array $location_rule
	 * Array (
	 *	[param] => block
	 *	[operator] => ==
	 *	[value] => acf/example
	 * )
	 * @return string
	 */
	private function get_location_helper_php( $location_rule ) { 
		
		// TODO Block partial is dependent on $location_rule
		
		ob_start();

		$location_slug = $location_rule['param'];

		$location_helper_partial = ACFTCP_Core::$plugin_path . 'pro/render/location-helpers/' . $location_slug . '.php';

		if ( file_exists( $location_helper_partial ) ) {
			include( $location_helper_partial );
		} 

		return ob_get_clean();

	}
}
