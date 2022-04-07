<?php

namespace FSVendor\WPDesk\Forms\Field;

class TimepickerField extends \FSVendor\WPDesk\Forms\Field\BasicField
{
    /**
     * @inheritDoc
     */
    public function get_template_name()
    {
        return 'timepicker';
    }
}
