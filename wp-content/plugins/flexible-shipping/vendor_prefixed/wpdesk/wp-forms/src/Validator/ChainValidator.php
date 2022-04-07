<?php

namespace FSVendor\WPDesk\Forms\Validator;

use FSVendor\WPDesk\Forms\Validator;
class ChainValidator implements \FSVendor\WPDesk\Forms\Validator
{
    /** @var Validator[] */
    private $validators;
    private $messages;
    public function __construct()
    {
        $this->validators = [];
        $this->messages = [];
    }
    /**
     * @param Validator $validator
     *
     * @return $this
     */
    public function attach(\FSVendor\WPDesk\Forms\Validator $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }
    public function is_valid($value)
    {
        $result = \true;
        $messages = [[]];
        foreach ($this->validators as $validator) {
            if (!$validator->is_valid($value)) {
                $result = \false;
                $messages[] = $validator->get_messages();
            }
        }
        $this->messages = \array_merge(...$messages);
        return $result;
    }
    public function get_messages()
    {
        return $this->messages;
    }
}
