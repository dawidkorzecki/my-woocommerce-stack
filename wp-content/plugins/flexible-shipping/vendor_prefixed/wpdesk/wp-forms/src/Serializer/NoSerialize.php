<?php

namespace FSVendor\WPDesk\Forms\Serializer;

use FSVendor\WPDesk\Forms\Serializer;
class NoSerialize implements \FSVendor\WPDesk\Forms\Serializer
{
    public function serialize($value)
    {
        return $value;
    }
    public function unserialize($value)
    {
        return $value;
    }
}
