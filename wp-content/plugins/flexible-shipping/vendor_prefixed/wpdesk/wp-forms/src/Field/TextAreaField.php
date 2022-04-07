<?php

namespace FSVendor\WPDesk\Forms\Field;

class TextAreaField extends \FSVendor\WPDesk\Forms\Field\BasicField
{
    public function __construct()
    {
        parent::__construct();
        $this->set_default_value('');
    }
    public function get_template_name()
    {
        return 'textarea';
    }
}
