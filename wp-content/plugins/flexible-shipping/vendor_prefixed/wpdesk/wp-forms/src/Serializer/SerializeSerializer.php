<?php

namespace FSVendor\WPDesk\Forms\Serializer;

use FSVendor\WPDesk\Forms\Serializer;
class SerializeSerializer implements \FSVendor\WPDesk\Forms\Serializer
{
    public function serialize($value)
    {
        return \serialize($value);
    }
    public function unserialize($value)
    {
        return \unserialize($value);
    }
}
