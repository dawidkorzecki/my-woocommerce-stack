<?php

namespace FSVendor\WPDesk\Forms\Field\Traits;

/**
 * Implementation of HTML attributes like id, name, action etc.
 *
 * @package WPDesk\Forms\Field\Traits
 */
trait HtmlAttributes
{
    /** @var string[] */
    protected $attributes;
    /**
     * Get list of all attributes except given.
     *
     * @param string[] $except
     *
     * @return string[]
     */
    public function get_attributes($except = ['name', 'type'])
    {
        return \array_filter($this->attributes, function ($value, $key) use($except) {
            return !\in_array($key, $except, \true);
        }, \ARRAY_FILTER_USE_BOTH);
    }
    /**
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function set_attribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    /**
     * @param string $name
     *
     * @return $this
     */
    public function unset_attribute($name)
    {
        unset($this->attributes[$name]);
        return $this;
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    public function is_attribute_set($name)
    {
        return isset($this->attributes[$name]);
    }
    /**
     * @param string $name
     * @param mixed $default
     *
     * @return string
     */
    public function get_attribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }
}
