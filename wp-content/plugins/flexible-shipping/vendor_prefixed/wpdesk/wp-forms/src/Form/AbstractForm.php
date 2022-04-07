<?php

namespace FSVendor\WPDesk\Forms\Form;

/**
 * Old abstraction layer for forms.
 *
 * @package WPDesk\Forms
 *
 * @deprecated Use ony for backward compatibility with Forms 1.x
 */
abstract class AbstractForm
{
    /**
     * Unique form_id.
     *
     * @var string
     */
    protected $form_id = 'form';
    /**
     * Updated data.
     *
     * @var array
     */
    protected $updated_data = array();
    /**
     * Checks if form should be active.
     *
     * @return bool
     */
    public function is_active()
    {
        return \true;
    }
    /**
     * Create form data and return an associative array.
     *
     * @return array
     */
    protected abstract function create_form_data();
    /**
     * Add array to update data.
     *
     * @param array $new_data new data to update.
     */
    public function update_form_data(array $new_data = array())
    {
        $this->updated_data = $new_data;
    }
    /**
     * Merge created and updated data and return associative array. Add to all keys form prefix.
     *
     * @return array
     */
    public function get_form_data()
    {
        return \array_merge($this->create_form_data(), $this->updated_data);
    }
    /**
     * Get prefixed array returns array with prefixed form_id
     *
     * @return array
     */
    public function get_prefixed_form_data()
    {
        $array = $this->get_form_data();
        $form_id = $this->get_form_id();
        return \array_combine(\array_map(function ($k) use($form_id) {
            return $form_id . '_' . $k;
        }, \array_keys($array)), $array);
    }
    /**
     * return form Id
     *
     * @return string
     */
    public function get_form_id()
    {
        return $this->form_id;
    }
}
