<?php

namespace FSVendor\WPDesk\Forms\Sanitizer;

use FSVendor\WPDesk\Forms\Sanitizer;
class CallableSanitizer implements \FSVendor\WPDesk\Forms\Sanitizer
{
    private $callable;
    public function __construct($callable)
    {
        $this->callable = $callable;
    }
    public function sanitize($value)
    {
        return \call_user_func($this->callable, $value);
    }
}
