<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string $value
 */
$header_size = (int) $field->get_meta_value('header_size') ?: 2;
$classes = $field->has_classes() ? 'class="' . \esc_attr($field->get_classes()) . '"' : '';
?>

<?php 
if ($field->has_label()) {
    ?>
	<h<?php 
    echo \esc_attr($header_size);
    ?> <?php 
    echo \esc_attr($classes);
    ?>><?php 
    echo \esc_html($field->get_label());
    ?></h<?php 
    echo \esc_attr($header_size);
    ?>>
<?php 
}
?>

<?php 
if ($field->has_description()) {
    ?>
	<p <?php 
    echo \esc_attr($classes);
    ?>><?php 
    echo \wp_kses_post($field->get_description());
    ?></p>
<?php 
}
