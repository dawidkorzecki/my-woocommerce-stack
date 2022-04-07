<?php

namespace FSVendor\WPDesk\Forms\Form;

use FSVendor\WPDesk\Forms\Form;
/**
 * FormsCollection class store AbstractForm instances and merges forms data from all collections
 *
 * @deprecated Use ony for backward compatibility with Forms 1.x
 *
 * @package WPDesk\Forms
 */
class FormsCollection
{
    /**
     * AbstractForm array collection.
     *
     * @var Form[]
     */
    protected $forms = array();
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
     * Add forms. All keys in this array must be unique, otherwise add_form will throw exception.
     *
     * @param Form[] $forms
     */
    public function add_forms(array $forms = array())
    {
        foreach ($forms as $form_object) {
            $this->add_form($form_object);
        }
    }
    /**
     * Add form. If key is not unique throw exception.
     *
     * @param Form $form
     *
     * @throws \OutOfBoundsException
     */
    public function add_form(\FSVendor\WPDesk\Forms\Form\AbstractForm $form)
    {
        if (!$this->is_form_exists($form->get_form_id())) {
            $this->forms[$form->get_form_id()] = $form;
        } else {
            throw new \OutOfBoundsException('Form with this key already exists');
        }
    }
    /**
     * Is form exists. Checks if key exists in the array of forms and return bool.
     *
     * @param string $form_id
     *
     * @return bool
     */
    public function is_form_exists($form_id)
    {
        return isset($this->forms[(string) $form_id]);
    }
    /**
     * Get form.
     *
     * @param string $form_id
     *
     * @return Form
     * @throws \OutOfRangeException
     */
    public function get_form($form_id)
    {
        if ($this->is_form_exists($form_id)) {
            return $this->forms[(string) $form_id];
        }
        throw new \OutOfRangeException('Form with this key not exists');
    }
    /**
     * Get forms data. This method merge all arrays from forms and return associative array for woocommerce form_fields.
     *
     * @param bool $prefixed if true add form_id as prefix to form keys
     *
     * @return array
     */
    public function get_forms_data($prefixed = \false)
    {
        $forms_data = array();
        foreach ($this->forms as $form) {
            if (!$form->is_active()) {
                continue;
            }
            if ($prefixed) {
                $forms_data = \array_merge($forms_data, $form->get_prefixed_form_data());
            } else {
                $forms_data = \array_merge($forms_data, $form->get_form_data());
            }
        }
        return $forms_data;
    }
}
