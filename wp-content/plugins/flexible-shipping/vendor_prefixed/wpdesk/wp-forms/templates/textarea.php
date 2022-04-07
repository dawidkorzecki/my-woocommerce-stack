<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string $value
 */
?>

<textarea
	id="<?php 
echo \esc_attr($field->get_id());
?>"
		<?php 
if ($field->has_classes()) {
    ?>
			class="<?php 
    echo \esc_attr($field->get_classes());
    ?>"<?php 
}
?>
	name="<?php 
echo \esc_attr($name_prefix);
?>[<?php 
echo \esc_attr($field->get_name());
?>]"
	<?php 
foreach ($field->get_attributes() as $key => $attr_val) {
    ?>
		<?php 
    echo \esc_attr($key);
    ?>="<?php 
    echo \esc_attr($attr_val);
    ?>"
	<?php 
}
?>

	<?php 
if ($field->is_required()) {
    ?>
		required="required"<?php 
}
?>
	<?php 
if ($field->is_disabled()) {
    ?>
		disabled="disabled"<?php 
}
?>
	<?php 
if ($field->is_readonly()) {
    ?>
		readonly="readonly"<?php 
}
?>
	<?php 
if ($field->is_multiple()) {
    ?>
		multiple="multiple"<?php 
}
?>

	<?php 
if ($field->has_placeholder()) {
    ?>
		placeholder="<?php 
    echo \esc_html($field->get_placeholder());
    ?>"<?php 
}
?>
><?php 
echo \esc_html($value);
?></textarea>
<?php 
