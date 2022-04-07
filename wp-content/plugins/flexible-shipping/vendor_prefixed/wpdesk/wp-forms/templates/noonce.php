<?php

namespace FSVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string $value
 */
\wp_nonce_field($field->get_meta_value('action'), $name_prefix . '[' . $field->get_name() . ']');
