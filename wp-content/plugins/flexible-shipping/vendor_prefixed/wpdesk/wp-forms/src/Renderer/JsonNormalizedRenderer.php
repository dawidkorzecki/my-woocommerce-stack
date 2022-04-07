<?php

namespace FSVendor\WPDesk\Forms\Renderer;

use FSVendor\WPDesk\Forms\FieldProvider;
use FSVendor\WPDesk\Forms\FieldRenderer;
/**
 * Can render form fields as JSON.
 *
 * @package WPDesk\Forms\Renderer
 */
class JsonNormalizedRenderer implements \FSVendor\WPDesk\Forms\FieldRenderer
{
    /**
     * @param FieldProvider $provider
     * @param array $fields_data
     * @param string $name_prefix
     *
     * @return array Normalized fields with data.
     */
    public function render_fields(\FSVendor\WPDesk\Forms\FieldProvider $provider, array $fields_data, $name_prefix = '')
    {
        $rendered_fields = [];
        foreach ($provider->get_fields() as $field) {
            $rendered = ['name' => $field->get_name(), 'template' => $field->get_template_name(), 'multiple' => $field->is_multiple(), 'disabled' => $field->is_disabled(), 'readonly' => $field->is_readonly(), 'required' => $field->is_required(), 'prefix' => $name_prefix, 'value ' => isset($fields_data[$field->get_name()]) ? $fields_data[$field->get_name()] : $field->get_default_value()];
            if ($field->has_classes()) {
                $rendered['class'] = $field->get_classes();
            }
            if ($field->has_description()) {
                $rendered['description'] = $field->get_description();
            }
            if ($field->has_description_tip()) {
                $rendered['description_tip'] = $field->get_description_tip();
            }
            if ($field->has_label()) {
                $rendered['label'] = $field->get_label();
            }
            if ($field->has_placeholder()) {
                $rendered['placeholder'] = $field->get_placeholder();
            }
            $options = $field->get_possible_values();
            if ($options) {
                $rendered['options'] = $options;
            }
            if ($field->has_data()) {
                $data = $field->get_data();
                $rendered['data'] = [];
                foreach ($data as $data_name => $data_value) {
                    $rendered['data'][] = ['name' => $data_name, 'value' => $data_value];
                }
            }
            if (\json_encode($rendered) !== \false) {
                $rendered_fields[] = $rendered;
            }
        }
        return $rendered_fields;
    }
}
