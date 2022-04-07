<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var \WPDesk\View\Renderer\Renderer $renderer
 * @var string $name_prefix
 * @var string $value
 * @var string $template_name Real field template.
 */
?>

<tr valign="top">
	<?php 
if ($field->has_label()) {
    ?>
		<?php 
    echo $renderer->render('form-label', ['field' => $field]);
    // phpcs:ignore
    ?>
	<?php 
}
?>

	<td class="forminp">
		<?php 
echo $renderer->render($template_name, ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $name_prefix, 'value' => $value]);
// phpcs:ignore
?>

		<?php 
if ($field->has_description()) {
    ?>
			<p class="description"><?php 
    echo \wp_kses_post($field->get_description());
    ?></p>
		<?php 
}
?>
	</td>
</tr>
<?php 
