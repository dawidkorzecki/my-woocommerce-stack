<?php

namespace FSVendor\WPDesk\Forms;

interface Validator
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function is_valid($value);
    /**
     * @return string[]
     */
    public function get_messages();
}
