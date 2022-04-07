<?php

namespace FSVendor;

/**
 * @var string $shipping_method_url
 * @var string $shipping_method_title
 */
echo \sprintf(\__('FS Debug mode for %1$s%2$s%3$s shipping method.', 'flexible-shipping'), '<a href="' . \esc_attr($shipping_method_url) . '" target="_blank">', \esc_html($shipping_method_title), '</a>');
?><br/>
<?php 
