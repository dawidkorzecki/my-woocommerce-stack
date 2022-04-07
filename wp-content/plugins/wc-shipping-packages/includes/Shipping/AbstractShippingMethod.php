<?php

/*********************************************************************/
/*  PROGRAM          FlexRC                                          */
/*  PROPERTY         3-7170 Ash Cres                                 */
/*  OF               Vancouver BC   V6P 3K7                          */
/*  				 Voice 604 800-7879                              */
/*  Any usage / copying / extension or modification without          */
/*  prior authorization is prohibited                                */
/*********************************************************************/

namespace OneTeamSoftware\WooCommerce\Shipping;

defined('ABSPATH') || exit;

if (!class_exists('\\OneTeamSoftware\\WooCommerce\\Shipping\\AbstractShippingMethod')):

abstract class AbstractShippingMethod extends \WC_Shipping_Method
{
	public function init_settings()
	{
		$this->settings = get_option($this->get_option_key(), null);

		// If there are no settings defined, use defaults.
		if (!is_array($this->settings)) {
			$this->settings = $this->getDefaultValues($this->get_form_fields());
		}
	}

	public function init_instance_settings()
	{
		$this->instance_settings = get_option($this->get_instance_option_key(), null);

		// If there are no settings defined, use defaults.
		if (!is_array($this->instance_settings)) {
			$this->instance_settings = $this->getDefaultValues($this->get_instance_form_fields());
		}
	}

	protected function getDefaultValues($fields)
	{
		$options = array();
		foreach ($fields as $key => $field) {
			if (isset($field['default'])) {
				$this->setKeyValue($options, $key, $field['default']);
			}
		}

		return $options;
	}

	// it can be overwritten in the child class to do additional validation of the new options
	protected function validate(array $options)
	{
		return true;
	}

	public function process_admin_options()
	{
		$this->init_settings();

		$postData = $this->get_post_data();
		$options = array();
		foreach ($this->get_form_fields() as $key => $field) {
			$isEnabled = true;
			if (isset($field['custom_attributes']['disabled']) && $field['custom_attributes']['disabled'] == 'yes') {
				$isEnabled = false;
			}

			if ('title' !== $this->get_field_type($field) && $isEnabled) {
				try {
					$value = $this->get_field_value($key, $field, $postData);

					$this->setKeyValue($options, $key, $value);

				} catch (\Exception $e) {
					$this->add_error($e->getMessage());
				}
			}
		}

		$success = false;
		if (!is_array($options)) {
			$options = array();
		}

		if ($this->validate($options)) {
			$this->settings = array_merge($this->settings, $options);

			$options = apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings);
			if (!empty($options) && is_array($options)) {
				$this->settings = $options;
			}

			$success = update_option($this->get_option_key(), $this->settings);
		}
		
		$this->display_errors();

		return $success;
	}

	protected function setKeyValue(&$options, $key, $value)
	{
		$keyParts = explode('[', $key);
		$option = &$options;

		for ($idx = 0; $idx < count($keyParts); ++$idx) {
			$keyPart = trim($keyParts[$idx], ']');
			$option = &$option[$keyPart];
		}

		$option = $value;
	}

	protected function getKeyValue($options, $key)
	{
		if (empty($options) || empty($key)) {
			return null;
		}

		$keyParts = explode('[', $key);
		$option = &$options;

		for ($idx = 0; !is_null($option) && $idx < count($keyParts); ++$idx) {
			$keyPart = trim($keyParts[$idx], ']');

			if (isset($option[$keyPart])) {
				$option = &$option[$keyPart];
			} else {
				$option = null;
			}
		}

		return $option;
	}

	public function get_field_value($key, $field, $postData = array())
	{
		$value = $this->getKeyValue($postData, $this->get_field_key($key));
		
		// Use filter defined for a given field
		if (!empty($field['filter'])) {
			$filter = $field['filter'];
			$filterOptions = isset($field['filter_options']) ? $field['filter_options'] : array();

			if (empty($value)) {
				if (empty($field['optional']) && $field['type'] != 'checkbox') {
					$value = false;
				}
			} else {
				$value = filter_var($value, $filter, $filterOptions);
			}

			if ($value === false && !empty($field['type']) && $field['type'] != 'checkbox') {
				throw new \Exception(sprintf('<strong>%s</strong> %s', $field['title'], __('is invalid', $this->id)));
			}

			return $value;
		}

		$type = $this->get_field_type($field);

		$fieldKeyMethodName = 'validate_' . $key . '_field';
		$fieldTypeMethodName = 'validate_' . $type . '_field';
		$fieldKeyFilterName = 'validate_' . $this->id . '_' . $key . '_field';
		$fieldTypeFilterName = 'validate_' . $this->id . '_' . $type . '_field';

		// Look for a validate_FIELDID_field method for special handling
		if (is_callable(array($this, $fieldKeyMethodName))) {
			return $this->{$fieldKeyMethodName}($key, $value);
		}

		// Look for a validate_FIELDTYPE_field method
		if (is_callable(array($this, $fieldTypeMethodName))) {
			return $this->{$fieldTypeMethodName}($key, $value);
		}

		// Look for validate_FIELDID_field filter
		if (has_filter($fieldKeyFilterName)) {
			return apply_filters($fieldKeyFilterName, $value, $key);
		}

		// Look for validate_FIELDTYPE_field filter
		if (has_filter($fieldTypeFilterName)) {
			return apply_filters($fieldTypeFilterName, $value, $key);
		}

		// Fallback to text
		return $this->validate_text_field($key, $value);
	}

	public function get_current_option($key, $defaultValue = null)
	{
		$value = null;
		if ($this->instance_id) {
			$value = $this->get_instance_option($key, $defaultValue);
		} else {
			$value = $this->get_option($key, $defaultValue);
		}

		return $value;
	}

	public function get_instance_option($key, $defaultValue = null)
	{
		if (empty($this->instance_settings)) {
			$this->init_instance_settings();
		}

		$value = $this->getKeyValue($this->instance_settings, $key);
		
		if (is_null($value) && is_null($defaultValue)) {
			$fields = $this->get_instance_form_fields();
			if (isset($fields[$key])) {
				$value = $this->get_field_default($fields[$key]);
			}
		}

		if (!is_null($defaultValue) && (is_null($value) || $value === '')) {
			$value = $defaultValue;
		}

		return $value;
	}

	public function get_option($key, $defaultValue = null)
	{
		if ($this->instance_id) {
			$value = $this->get_instance_option($key);

			if (!is_null($value) && $value !== '') {
				return $value;
			}
		}

		if (empty($this->settings)) {
			$this->init_settings();
		}
		
		$value = $this->getKeyValue($this->settings, $key);
		
		if (is_null($value) && is_null($defaultValue)) {
			$fields = $this->get_form_fields();
			if (isset($fields[$key])) {
				$value = $this->get_field_default($fields[$key]);
			}
		}
		
		if (!is_null($defaultValue) && (is_null($value) || $value === '')) {
			$value = $defaultValue;
		}
		
		return $value;
	}

	public function generate_settings_html($formFields = array(), $echo = true)
	{
		if (empty($formFields)) {
			$formFields = $this->get_form_fields();
		}

		$html = '';
		foreach ($formFields as $key => $field) {
			$html .= $this->generate_field_html($key, $field);
		}

		if ($echo) {
			echo $html;
		}

		return $html;
	}

	public function generate_field_html($key, $field = array())
	{
		$field = apply_filters('generate_' . $this->id . '_field_data', $field, $key);

		$type = $this->get_field_type($field);
		$value = $this->get_current_option($key, null);
		if (isset($value)) {
			$field['default'] = $value;
		}

		$methodName = 'generate_' . $type . '_html';
		
		$html = '';
		if (method_exists($this, $methodName)) {
			$html = $this->{$methodName}($key, $field);
		} else {
			$html = $this->generate_text_html($key, $field);
		}

		$filterName = 'generate_' . $this->id . '_' . $type . '_html';
		if (has_filter($filterName)) {
			$html = apply_filters($filterName, $html, $key, $field);
		}

		return $html;
	}

	public function get_field_key($key)
	{
		$fieldKey = $this->plugin_id . $this->id . '_' . $key;
		$fieldKey = apply_filters('get_' . $this->id . '_field_key', $fieldKey, $key);

		return $fieldKey;
	}
}

endif;