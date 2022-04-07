<?php

/**
 * WooCommerce settings fields modifier.
 *
 * @package WPDesk\Beacon\Beacon
 */
namespace FSVendor\WPDesk\Beacon\Beacon;

/**
 * Can modify WooCommerce settings fields.
 * Use it on WooCommerce settings fields for Beacon search functionality.
 */
class WooCommerceSettingsFieldsModifier
{
    const FIELD_CLASS = 'class';
    const FIELD_CUSTOM_ATTRIBUTES = 'custom_attributes';
    const FIELD_TITLE = 'title';
    const CLASS_HS_BEACON_SEARCH = 'hs-beacon-search';
    const DATA_BEACON_SEARCH = 'data-beacon_search';
    /**
     * Appends beacon search data to fields.
     * It takes field title and set it as beacon search.
     *
     * @param array $form_fields .
     *
     * @return array
     */
    public function append_beacon_search_data_to_fields(array $form_fields)
    {
        foreach ($form_fields as $field_name => $field) {
            if (isset($field[self::FIELD_TITLE])) {
                if (empty($field[self::FIELD_CLASS])) {
                    $field[self::FIELD_CLASS] = self::CLASS_HS_BEACON_SEARCH;
                } else {
                    $field[self::FIELD_CLASS] .= ' ' . self::CLASS_HS_BEACON_SEARCH;
                }
                if (!isset($field[self::FIELD_CUSTOM_ATTRIBUTES])) {
                    $field[self::FIELD_CUSTOM_ATTRIBUTES] = array();
                }
                $field[self::FIELD_CUSTOM_ATTRIBUTES][self::DATA_BEACON_SEARCH] = $field[self::FIELD_TITLE];
            }
            $form_fields[$field_name] = $field;
        }
        return $form_fields;
    }
}
