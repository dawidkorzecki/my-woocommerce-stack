<?php

namespace FSVendor\WPDesk\Forms\Validator;

use FSVendor\WPDesk\Forms\Validator;
class RequiredValidator implements \FSVendor\WPDesk\Forms\Validator
{
    public function is_valid($value)
    {
        return $value !== null;
    }
    public function get_messages()
    {
        return [];
    }
}
