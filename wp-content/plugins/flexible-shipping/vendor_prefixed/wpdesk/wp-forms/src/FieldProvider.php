<?php

namespace FSVendor\WPDesk\Forms;

/**
 * FieldProvider is owner of FormFields. These fields can be used to render forms and process values.
 */
interface FieldProvider
{
    /**
     * Returns owned fields.
     *
     * @return Field[]
     */
    public function get_fields();
}
