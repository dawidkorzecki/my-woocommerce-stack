<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var mixed $value
 */
?>

<select
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
?>]<?php 
echo \esc_attr($field->is_multiple()) ? '[]' : '';
?>"
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
>
	<?php 
if ($field->has_placeholder()) {
    ?>
		<option value=""><?php 
    echo \esc_html($field->get_placeholder());
    ?></option><?php 
}
?>

	<?php 
foreach ($field->get_possible_values() as $possible_value => $label) {
    ?>
		<option
			<?php 
    if ($possible_value === $value || \is_array($value) && \in_array($possible_value, $value, \true) || \is_numeric($possible_value) && \is_numeric($value) && (int) $possible_value === (int) $value) {
        ?>
				selected="selected"<?php 
    }
    ?>
			value="<?php 
    echo \esc_attr($possible_value);
    ?>"
		><?php 
    echo \esc_html($label);
    ?></option>
	<?php 
}
?>
</select>
<?php 
