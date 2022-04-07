<?php

namespace FSVendor\WPDesk\Forms;

use FSVendor\WPDesk\Forms\Field\BasicField;
/**
 * The idea is that from the moment the factory returns this interface it's values cannot be changed.
 * And that is why here are only the getters.
 *
 * The: Validation, Serialization, Sanitization features are provided trough delegated classes (get_validator, get_serializer ...)
 *
 * @package WPDesk\Forms
 */
interface Field
{
    /** @return string */
    public function get_name();
    /** @return mixed */
    public function get_default_value();
    /** @return string */
    public function get_template_name();
    /**
     * When this field is used on form this field will force it's own template.
     *
     * return bool
     */
    public function should_override_form_template();
    /**
     * HTML label.
     *
     * @return string
     */
    public function get_label();
    /** bool */
    public function has_label();
    /**
     * Description for field. It can be shown near the field.
     *
     * @return string
     */
    public function get_description();
    /**
     * Additional field description that should be shown in optional hover tip.
     *
     * @return string
     */
    public function get_description_tip();
    /** @return bool */
    public function has_description_tip();
    /** @return bool */
    public function has_description();
    /**
     * @return bool
     */
    public function is_readonly();
    /** @return bool */
    public function is_disabled();
    /** @return string */
    public function get_id();
    /** @bool */
    public function is_required();
    /** @return bool */
    public function has_placeholder();
    /** @return string */
    public function get_placeholder();
    /**
     * @param string[] $except
     *
     * @return string[] name->value
     */
    public function get_attributes($except = []);
    /**
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function get_attribute($name, $default = null);
    /** @return bool */
    public function is_attribute_set($name);
    /**
     * @param string $name
     *
     * @return string
     */
    public function get_meta_value($name);
    /** @return bool */
    public function is_meta_value_set($name);
    /**
     * @return string
     */
    public function get_classes();
    /** bool */
    public function has_classes();
    /** @return bool */
    public function is_class_set($name);
    /** bool */
    public function has_data();
    /**
     * @return array
     */
    public function get_data();
    /**
     * @param string $data_name
     * @param string $data_value
     *
     * @return $this
     */
    public function add_data($data_name, $data_value);
    /**
     * @param string $data_name
     *
     * @return $this
     */
    public function unset_data($data_name);
    /**
     * @return mixed
     */
    public function get_possible_values();
    /**
     * @return bool
     */
    public function is_multiple();
    /**
     * @return Validator
     */
    public function get_validator();
    /**
     * @return Sanitizer
     */
    public function get_sanitizer();
    /** @return Serializer */
    public function get_serializer();
}
