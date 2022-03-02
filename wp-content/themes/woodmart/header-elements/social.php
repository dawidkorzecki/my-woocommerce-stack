<?php
woodmart_enqueue_inline_style( 'header-elements-base' );
$params['style'] = ( ! $params['style'] ) ? 'default' : $params['style'];
echo woodmart_shortcode_social($params);
