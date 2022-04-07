<?php

namespace FSVendor\WPDesk\Forms\Field;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\Sanitizer\NoSanitize;
use FSVendor\WPDesk\Forms\Serializer;
use FSVendor\WPDesk\Forms\Serializer\NoSerialize;
use FSVendor\WPDesk\Forms\Validator\ChainValidator;
use FSVendor\WPDesk\Forms\Validator\RequiredValidator;
/**
 * Base class for fields. Is responsible for settings all required field values and provides standard implementation for
 * the field interface.
 *
 * @package WPDesk\Forms
 */
abstract class BasicField implements \FSVendor\WPDesk\Forms\Field
{
    use Field\Traits\HtmlAttributes;
    /** @var array[] */
    protected $meta;
    protected $default_value;
    public function __construct()
    {
        $this->meta['class'] = [];
    }
    public function get_label()
    {
        return $this->meta['label'];
    }
    /**
     * @param string $value
     *
     * @return $this
     */
    public function set_label($value)
    {
        $this->meta['label'] = $value;
        return $this;
    }
    public function get_description_tip()
    {
        return $this->meta['description_tip'];
    }
    public function has_description_tip()
    {
        return isset($this->meta['description_tip']);
    }
    public function should_override_form_template()
    {
        return isset($this->attributes['overrite_template']) ? $this->attributes['overrite_template'] : \false;
    }
    public function get_description()
    {
        return $this->meta['description'];
    }
    public function has_label()
    {
        return isset($this->meta['label']);
    }
    public function has_description()
    {
        return isset($this->meta['description']);
    }
    public function set_description($value)
    {
        $this->meta['description'] = $value;
        return $this;
    }
    public function set_description_tip($value)
    {
        $this->meta['description_tip'] = $value;
        return $this;
    }
    /**
     * @return array
     *
     * @deprecated not sure if needed. TODO: Check later.
     */
    public function get_type()
    {
        return $this->attributes['type'];
    }
    /**
     * @param string $value
     *
     * @return $this
     */
    public function set_placeholder($value)
    {
        $this->meta['placeholder'] = $value;
        return $this;
    }
    public function has_placeholder()
    {
        return isset($this->meta['placeholder']);
    }
    public function get_placeholder()
    {
        return $this->meta['placeholder'];
    }
    /**
     * @param string $name
     *
     * @return $this
     */
    public function set_name($name)
    {
        $this->attributes['name'] = $name;
        return $this;
    }
    public function get_meta_value($name)
    {
        return $this->meta[$name];
    }
    public function get_classes()
    {
        return \implode(' ', $this->meta['class']);
    }
    public function has_classes()
    {
        return !empty($this->meta['class']);
    }
    public function has_data()
    {
        return !empty($this->meta['data']);
    }
    /**
     * @return array
     */
    public function get_data()
    {
        return empty($this->meta['data']) ? [] : $this->meta['data'];
    }
    public function get_possible_values()
    {
        return isset($this->meta['possible_values']) ? $this->meta['possible_values'] : [];
    }
    public function get_id()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : \sanitize_title($this->get_name());
    }
    public function get_name()
    {
        return $this->attributes['name'];
    }
    public function is_multiple()
    {
        return isset($this->attributes['multiple']) ? $this->attributes['multiple'] : \false;
    }
    /**
     * @return $this
     */
    public function set_disabled()
    {
        $this->attributes['disabled'] = \true;
        return $this;
    }
    public function is_disabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : \false;
    }
    /**
     * @return $this
     */
    public function set_readonly()
    {
        $this->attributes['readonly'] = \true;
        return $this;
    }
    public function is_readonly()
    {
        return isset($this->attributes['readonly']) ? $this->attributes['readonly'] : \false;
    }
    /**
     * @return $this
     */
    public function set_required()
    {
        $this->meta['required'] = \true;
        return $this;
    }
    /**
     * @param string $class_name
     *
     * @return $this
     */
    public function add_class($class_name)
    {
        $this->meta['class'][$class_name] = $class_name;
        return $this;
    }
    /**
     * @param string $class_name
     *
     * @return $this
     */
    public function unset_class($class_name)
    {
        unset($this->meta['class'][$class_name]);
        return $this;
    }
    /**
     * @param string $data_name
     * @param string $data_value
     *
     * @return $this
     */
    public function add_data($data_name, $data_value)
    {
        if (!isset($this->meta['data'])) {
            $this->meta['data'] = [];
        }
        $this->meta['data'][$data_name] = $data_value;
        return $this;
    }
    /**
     * @param string $data_name
     *
     * @return $this
     */
    public function unset_data($data_name)
    {
        unset($this->meta['data'][$data_name]);
        return $this;
    }
    public function is_meta_value_set($name)
    {
        return isset($this->meta[$name]);
    }
    public function is_class_set($name)
    {
        return isset($this->meta['class'][$name]);
    }
    public function get_default_value()
    {
        return $this->default_value;
    }
    /**
     * @param string $value
     *
     * @return $this
     */
    public function set_default_value($value)
    {
        $this->default_value = $value;
        return $this;
    }
    /**
     * @return ChainValidator
     */
    public function get_validator()
    {
        $chain = new \FSVendor\WPDesk\Forms\Validator\ChainValidator();
        if ($this->is_required()) {
            $chain->attach(new \FSVendor\WPDesk\Forms\Validator\RequiredValidator());
        }
        return $chain;
    }
    public function is_required()
    {
        return isset($this->meta['required']) ? $this->meta['required'] : \false;
    }
    public function get_sanitizer()
    {
        return new \FSVendor\WPDesk\Forms\Sanitizer\NoSanitize();
    }
    /**
     * @return Serializer
     */
    public function get_serializer()
    {
        if (isset($this->meta['serializer']) && $this->meta['serializer'] instanceof \FSVendor\WPDesk\Forms\Serializer) {
            return $this->meta['serializer'];
        }
        return new \FSVendor\WPDesk\Forms\Serializer\NoSerialize();
    }
    public function set_serializer(\FSVendor\WPDesk\Forms\Serializer $serializer)
    {
        $this->meta['serializer'] = $serializer;
        return $this;
    }
}
