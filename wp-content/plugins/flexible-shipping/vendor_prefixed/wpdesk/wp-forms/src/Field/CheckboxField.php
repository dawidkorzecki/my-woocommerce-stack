<?php

namespace FSVendor\WPDesk\Forms\Field;

class CheckboxField extends \FSVendor\WPDesk\Forms\Field\BasicField
{
    const VALUE_TRUE = 'yes';
    const VALUE_FALSE = 'no';
    public function __construct()
    {
        parent::__construct();
        $this->set_attribute('type', 'checkbox');
    }
    public function get_template_name()
    {
        return 'input-checkbox';
    }
    public function get_sublabel()
    {
        return $this->meta['sublabel'];
    }
    public function set_sublabel($value)
    {
        $this->meta['sublabel'] = $value;
        return $this;
    }
    public function has_sublabel()
    {
        return isset($this->meta['sublabel']);
    }
}
