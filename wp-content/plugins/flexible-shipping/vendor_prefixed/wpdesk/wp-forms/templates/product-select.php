<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string[] $value
 */
?>

<select class="wc-product-search" multiple="multiple" style="width: 50%;"
		id="<?php 
echo \esc_attr($field->get_id());
?>"
		name="<?php 
echo \esc_attr($name_prefix);
?>[<?php 
echo \esc_attr($field->get_name());
?>][]"
		data-placeholder="<?php 
\esc_attr_e('Search for a product&hellip;', 'flexible-shipping');
?>"
		data-action="woocommerce_json_search_products_and_variations">
	<?php 
foreach ((array) $value as $product_id) {
    $product = \wc_get_product($product_id);
    if (\is_object($product)) {
        echo '<option value="' . \esc_attr($product_id) . '"' . \selected(\true, \true, \false) . '>' . \wp_kses_post($product->get_formatted_name()) . '</option>';
    }
}
?>
</select>

<script type="text/javascript">
	jQuery(document.body).trigger('wc-enhanced-select-init');
</script>
<?php 
