<?php

namespace FSVendor\WPDesk\Forms;

interface Escaper
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function escape($value);
}
