<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string $value
 */
\wp_print_styles('media-views');
?>

<script>
	window.SM_EditorInitialized = true;
</script>


<?php 
$editor_id = \uniqid('wyswig_');
$editor_settings = ['textarea_name' => \esc_attr($name_prefix) . '[' . \esc_attr($field->get_name()) . ']'];
\wp_editor(\wp_kses_post($value), $editor_id, $editor_settings);
?>
<script type="text/javascript">
	(function () {
		ShopMagic.wyswig.init('<?php 
echo \esc_attr($editor_id);
?>');
	}());
</script>
<?php 
