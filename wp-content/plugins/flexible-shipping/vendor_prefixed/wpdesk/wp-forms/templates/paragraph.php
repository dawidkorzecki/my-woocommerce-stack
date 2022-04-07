<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string $value
 */
if ($field->has_description()) {
    ?>
	<tr>
		<td style="padding-left:0;" colspan="2">
			<p
			<?php 
    if ($field->has_classes()) {
        ?>
				class="<?php 
        echo \esc_attr($field->get_classes());
        ?>"<?php 
    }
    ?>><?php 
    echo \wp_kses_post($field->get_description());
    ?></p>
		</td>
	</tr>
<?php 
}
