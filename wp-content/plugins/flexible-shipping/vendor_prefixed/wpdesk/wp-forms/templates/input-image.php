<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string              $name_prefix
 * @var string              $value
 */
$media_container_id = 'media_' . \sanitize_key($field->get_id());
?>
<div class="media-input-wrapper" id="<?php 
echo \esc_attr($media_container_id);
?>">
	<input type="hidden" class="image-field-value" value="<?php 
echo \esc_html($value);
?>"
			name="<?php 
echo \esc_attr($name_prefix) . '[' . \esc_attr($field->get_name()) . ']';
?>"
			id="<?php 
echo \esc_attr($field->get_id());
?>"/>
	<div class="custom-img-container">
		<?php 
if ($value) {
    ?>
			<img src="<?php 
    echo \esc_url($value);
    ?>" alt="" width="100"/>
		<?php 
}
?>
	</div>
	<p class="hide-if-no-js">
		<a class="upload-custom-img 
		<?php 
if ($value) {
    ?>
			hidden<?php 
}
?>" href="<?php 
echo \esc_url($value);
?>">
			<?php 
\esc_html_e('Set image', 'flexible-shipping');
?>
		</a>
		<a class="delete-custom-img 
		<?php 
if (!$value) {
    ?>
			hidden<?php 
}
?>" href="#">
			<?php 
\esc_html_e('Remove image', 'flexible-shipping');
?>
		</a>
	</p>
</div>
<script>
	jQuery( function ( $ ) {
		var frame,
			metaBox = $( '#<?php 
echo \esc_attr($media_container_id);
?>' ),
			addImgLink = metaBox.find( '.upload-custom-img' ),
			delImgLink = metaBox.find( '.delete-custom-img' ),
			imgContainer = metaBox.find( '.custom-img-container' ),
			imgIdInput = metaBox.find( '.image-field-value' );

		addImgLink.on( 'click', function ( event ) {
			event.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: <?php 
\esc_html_e('Select or Upload Media', 'flexible-shipping');
?>,
				button: {
					text: <?php 
\esc_html_e('Use this media', 'flexible-shipping');
?>
				},
				library: {
					type: ['image']
				},
				multiple: false
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				imgContainer.append( '<img src="' + attachment.url + '" alt="" width="100" />' );
				imgIdInput.val( attachment.url );
				addImgLink.addClass( 'hidden' );
				delImgLink.removeClass( 'hidden' );
			} );
			frame.open();
		} );

		delImgLink.on( 'click', function () {
			imgContainer.html( '' );
			addImgLink.removeClass( 'hidden' );
			delImgLink.addClass( 'hidden' );
			imgIdInput.val( '' );
			return false;
		} );

	} );
</script>
<?php 
