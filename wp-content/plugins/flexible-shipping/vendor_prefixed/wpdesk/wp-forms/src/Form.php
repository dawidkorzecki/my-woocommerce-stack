<?php

namespace FSVendor\WPDesk\Forms;

use FSVendor\WPDesk\View\Renderer\Renderer;
/**
 * Abstraction layer for forms.
 *
 * @package WPDesk\Forms
 */
interface Form
{
    /**
     * For some reason you may want to disable a form. Returns false when disabled.
     *
     * @return bool
     */
    public function is_active();
    /**
     * Whether form handle_request method was successfully executed.
     *
     * @return bool
     */
    public function is_submitted();
    /**
     * After handle_request or set_data the data in form can be invalid according to field validators.
     * Returns false when onle of them says the data is invalid.
     *
     * @return bool
     */
    public function is_valid();
    /**
     * Add array to update data.
     *
     * @param array $request New data to update.
     */
    public function handle_request($request = array());
    /**
     * Data could be saved in some place. Use this method to transmit them to form.
     *
     * @param array $data Data for form.
     */
    public function set_data($data);
    /**
     * Use to render the form to string.
     *
     * @param Renderer $renderer Renderer to render form fields and form-templates.
     *
     * @return string
     */
    public function render_form(\FSVendor\WPDesk\View\Renderer\Renderer $renderer);
    /**
     * Get data from form. Use after handle_request or set_data.
     *
     * @return array
     */
    public function get_data();
    /**
     * Get data from form. Use after handle_request or set_data.
     * The difference get_data is that here you will not get any objects and complex data types besides arrays.
     *
     * @return array
     */
    public function get_normalized_data();
    /**
     * Form if you ever need to have more than one form at once.
     *
     * @return string
     */
    public function get_form_id();
}
