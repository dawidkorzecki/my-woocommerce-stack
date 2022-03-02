<?php
// Group field (based on the repeater field)

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// ACFTCP_Group arguments
$field_group_id = $this->id;
$fields = NULL;
$nesting_arg = 0;
$sub_field_indent_count = $this->indent_count + ACFTCP_Core::$indent_repeater;
$field_location = '';

$args = array(
	'field_group_id' => $field_group_id,
	'fields' => $fields,
	'nesting_level' => $nesting_arg + 1,
	'indent_count' => $sub_field_indent_count,
	'location' => $field_location,
	'exclude_html_wrappers' => $this->exclude_html_wrappers
);
$group_field_group = new ACFTCP_Group( $args );

// If group field has sub fields
if ( !empty( $group_field_group->fields ) ) {

	echo $this->indent . htmlspecialchars("<?php if ( have_rows( '" . $this->name ."'". $this->location . " ) ) : ?>")."\n";
	echo $this->indent . htmlspecialchars("	<?php while ( have_rows( '" . $this->name ."'". $this->location . " ) ) : the_row(); ?>")."\n";

	echo $group_field_group->get_field_group_html();

	echo $this->indent . htmlspecialchars("	<?php endwhile; ?>")."\n";
	echo $this->indent . htmlspecialchars("<?php endif; ?>")."\n";

}
// Group field has no sub fields
else {

	echo $this->indent . htmlspecialchars("<?php // warning: group '" . $this->name . "' has no sub fields ?>")."\n";

}
