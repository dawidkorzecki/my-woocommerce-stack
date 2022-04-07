<?php

namespace FSVendor\WPDesk\Forms\Serializer;

use FSVendor\WPDesk\Forms\Serializer;
class JsonSerializer implements \FSVendor\WPDesk\Forms\Serializer
{
    public function serialize($value)
    {
        return \json_encode($value);
    }
    public function unserialize($value)
    {
        return \json_decode($value, \true);
    }
}
