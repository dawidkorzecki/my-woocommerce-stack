<?php

namespace FSVendor\WPDesk\Forms\Validator;

use FSVendor\WPDesk\Forms\Validator;
class NoValidateValidator implements \FSVendor\WPDesk\Forms\Validator
{
    public function is_valid($value)
    {
        return \true;
    }
    public function get_messages()
    {
        return [];
    }
}
