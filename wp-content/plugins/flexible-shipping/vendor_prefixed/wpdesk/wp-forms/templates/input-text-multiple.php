<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var \WPDesk\View\Renderer\Renderer $renderer
 * @var string $name_prefix
 * @var string $value
 * @var string $template_name Real field template.
 */
if (empty($value) || \is_string($value)) {
    $input_values[] = '';
} else {
    $input_values = $value;
}
?>
<div class="clone-element-container">
<?php 
foreach ($input_values as $text_value) {
    ?>
	<?php 
    if (!\in_array($field->get_type(), ['number', 'text', 'hidden'], \true)) {
        ?>
	<input type="hidden" name="<?php 
        echo \esc_attr($name_prefix) . '[' . \esc_attr($field->get_name()) . ']';
        ?>" value="no"/>
<?php 
    }
    ?>

	<?php 
    if ($field->get_type() === 'checkbox' && $field->has_sublabel()) {
        ?>
		<label><?php 
    }
    ?>
	<div class="clone-wrapper">
	<input
		type="<?php 
    echo \esc_attr($field->get_type());
    ?>"
		name="<?php 
    echo \esc_attr($name_prefix) . '[' . \esc_attr($field->get_name()) . '][]';
    ?>"
		id="<?php 
    echo \esc_attr($field->get_id());
    ?>"

		<?php 
    if ($field->has_classes()) {
        ?>
			class="<?php 
        echo \esc_attr($field->get_classes());
        ?>"
		<?php 
    }
    ?>

		<?php 
    if ($field->get_type() === 'text' && $field->has_placeholder()) {
        ?>
			placeholder="<?php 
        echo \esc_html($field->get_placeholder());
        ?>"
		<?php 
    }
    ?>

		<?php 
    foreach ($field->get_attributes() as $key => $atr_val) {
        echo \esc_attr($key) . '="' . \esc_attr($atr_val) . '"';
        ?>
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
    if (\in_array($field->get_type(), ['number', 'text', 'hidden'], \true)) {
        ?>
			value="<?php 
        echo \esc_html($text_value);
        ?>"
		<?php 
    } else {
        ?>
			value="yes"
			<?php 
        if ($value === 'yes') {
            ?>
				checked="checked"
			<?php 
        }
        ?>
		<?php 
    }
    ?>
	/>
		<span class="add-field"><span class="dashicons dashicons-plus-alt"></span></span>
		<span class="remove-field hidden"><span class="dashicons dashicons-remove"></span></span>
	</div>

	<?php 
    if ($field->get_type() === 'checkbox' && $field->has_sublabel()) {
        ?>
		<?php 
        echo \esc_html($field->get_sublabel());
        ?></label>
<?php 
    }
}
?>
</div>
<style>
	.clone-element-container .clone-wrapper .add-field {
		display: none;
	}
	.clone-element-container .clone-wrapper:first-child .add-field {
		display: inline-block;
	}

	.clone-element-container .clone-wrapper .remove-field {
		display: inline-block;
	}
	.clone-element-container .clone-wrapper:first-child .remove-field {
		display: none;
	}
</style>
<script>
	jQuery( function ( $ ) {
		var add_field = jQuery( '.add-field' );
		if ( add_field.length ) {
			add_field.click( function ( e ) {
				let html = jQuery( this ).closest( '.clone-wrapper' ).clone();
				html.find( 'input' ).val( '' );
				jQuery( this ).closest( '.clone-wrapper' ).after( html );
			} )

			jQuery( '.clone-element-container' ).on( "click", ".remove-field", function ( e ) {
				let is_disabled = jQuery( this ).hasClass( 'field-disabled' );
				if ( !is_disabled ) {
					jQuery( this ).closest( '.clone-wrapper' ).remove();
				}
			} )

			jQuery( '.form-table' ).find( 'input,select' ).each( function ( i, v ) {
				let disabled = jQuery( this ).attr( 'data-disabled' );
				if ( disabled === 'yes' ) {
					jQuery( this ).attr( 'disabled', 'disabled' )
					jQuery( this ).parent().addClass( 'field-disabled' );
				}
			} );
		}
	} );
</script>
<?php 
