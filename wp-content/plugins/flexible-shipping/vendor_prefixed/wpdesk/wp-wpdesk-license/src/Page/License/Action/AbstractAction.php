<?php

namespace FSVendor\WPDesk\License\Page\License\Action;

/**
 * Abstract Action with error handling methods.
 */
abstract class AbstractAction
{
    /**
     * @var bool
     */
    private $add_settings_error;
    /**
     * @var ActionError[]
     */
    private $errors = [];
    /**
     * @param bool $add_settings_error .
     */
    public function __construct(bool $add_settings_error)
    {
        $this->add_settings_error = $add_settings_error;
    }
    /**
     * @return ActionError[]
     */
    public function get_errors() : array
    {
        return $this->errors;
    }
    /**
     * Adds error.
     *
     * @param string $setting .
     * @param string $code .
     * @param string $message .
     * @param string $type .
     */
    protected function add_error(string $setting, string $code, string $message, string $type = 'error')
    {
        if ($this->add_settings_error) {
            \add_settings_error($setting, $code, $message, $type);
        } else {
            $this->errors[] = new \FSVendor\WPDesk\License\Page\License\Action\ActionError($message, $type);
        }
    }
}
