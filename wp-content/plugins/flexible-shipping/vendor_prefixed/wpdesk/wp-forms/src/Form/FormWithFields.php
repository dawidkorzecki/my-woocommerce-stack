<?php

namespace FSVendor\WPDesk\Forms\Form;

use Psr\Container\ContainerInterface;
use FSVendor\WPDesk\Forms\ContainerForm;
use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\FieldProvider;
use FSVendor\WPDesk\Forms\Form;
use FSVendor\WPDesk\Persistence\Adapter\ArrayContainer;
use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
use FSVendor\WPDesk\View\Renderer\Renderer;
class FormWithFields implements \FSVendor\WPDesk\Forms\Form, \FSVendor\WPDesk\Forms\ContainerForm, \FSVendor\WPDesk\Forms\FieldProvider
{
    use Field\Traits\HtmlAttributes;
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
    private $updated_data;
    /**
     * Form fields.
     *
     * @var Field[]
     */
    private $fields;
    /**
     * FormWithFields constructor.
     *
     * @param array $fields Form fields.
     * @param string $form_id Unique form id.
     */
    public function __construct(array $fields, $form_id = 'form')
    {
        $this->fields = $fields;
        $this->form_id = $form_id;
        $this->updated_data = null;
    }
    /**
     * Set Form action attribute.
     *
     * @param string $action
     */
    public function set_action($action)
    {
        $this->attributes['action'] = $action;
        return $this;
    }
    /**
     * Set Form method attribute ie. GET/POST.
     *
     * @param string $method
     */
    public function set_method($method)
    {
        $this->attributes['method'] = $method;
        return $this;
    }
    /**
     * @return string
     */
    public function get_method()
    {
        return isset($this->attributes['method']) ? $this->attributes['method'] : 'POST';
    }
    /**
     * @return string
     */
    public function get_action()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : '';
    }
    /**
     * @inheritDoc
     */
    public function is_submitted()
    {
        return null !== $this->updated_data;
    }
    /**
     * @inheritDoc
     */
    public function add_field(\FSVendor\WPDesk\Forms\Field $field)
    {
        $this->fields[] = $field;
    }
    /**
     * @inheritDoc
     */
    public function is_active()
    {
        return \true;
    }
    /**
     * Add more fields to form.
     *
     * @param Field[] $fields Field to add to form.
     */
    public function add_fields(array $fields)
    {
        \array_map([$this, 'add_field'], $fields);
    }
    /**
     * @inheritDoc
     */
    public function is_valid()
    {
        foreach ($this->fields as $field) {
            $field_value = isset($this->updated_data[$field->get_name()]) ? $this->updated_data[$field->get_name()] : $field->get_default_value();
            $field_validator = $field->get_validator();
            if (!$field_validator->is_valid($field_value)) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Add array to update data.
     *
     * @param array|ContainerInterface $request new data to update.
     */
    public function handle_request($request = array())
    {
        if ($this->updated_data === null) {
            $this->updated_data = [];
        }
        foreach ($this->fields as $field) {
            $data_key = $field->get_name();
            if (isset($request[$data_key])) {
                $this->updated_data[$data_key] = $field->get_sanitizer()->sanitize($request[$data_key]);
            }
        }
    }
    /**
     * Data could be saved in some place. Use this method to transmit them to form.
     *
     * @param array|ContainerInterface $data Data consistent with Form and ContainerForm interface.
     */
    public function set_data($data)
    {
        if (\is_array($data)) {
            $data = new \FSVendor\WPDesk\Persistence\Adapter\ArrayContainer($data);
        }
        foreach ($this->fields as $field) {
            $data_key = $field->get_name();
            if ($data->has($data_key)) {
                try {
                    $this->updated_data[$data_key] = $data->get($data_key);
                } catch (\FSVendor\WPDesk\Persistence\ElementNotExistsException $e) {
                    $this->updated_data[$data_key] = \false;
                }
            }
        }
    }
    /**
     * Renders only fields without form.
     *
     * @param Renderer $renderer
     *
     * @return string
     */
    public function render_fields(\FSVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $content = '';
        $fields_data = $this->get_data();
        foreach ($this->get_fields() as $field) {
            $content .= $renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $this->get_form_id(), 'value' => isset($fields_data[$field->get_name()]) ? $fields_data[$field->get_name()] : $field->get_default_value(), 'template_name' => $field->get_template_name()]);
        }
        return $content;
    }
    /**
     * @inheritDoc
     */
    public function render_form(\FSVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $content = $renderer->render('form-start', [
            'form' => $this,
            'method' => $this->get_method(),
            // backward compat
            'action' => $this->get_action(),
        ]);
        $content .= $this->render_fields($renderer);
        $content .= $renderer->render('form-end');
        return $content;
    }
    /**
     * @inheritDoc
     */
    public function put_data(\FSVendor\WPDesk\Persistence\PersistentContainer $container)
    {
        foreach ($this->get_fields() as $field) {
            $data_key = $field->get_name();
            if (empty($data_key)) {
                continue;
            }
            if (!isset($this->updated_data[$data_key])) {
                $container->set($data_key, $field->get_default_value());
            } else {
                $container->set($data_key, $this->updated_data[$data_key]);
            }
        }
    }
    /**
     * @inheritDoc
     */
    public function get_data()
    {
        $data = $this->updated_data;
        foreach ($this->get_fields() as $field) {
            $data_key = $field->get_name();
            if (!isset($data[$data_key])) {
                $data[$data_key] = $field->get_default_value();
            }
        }
        return $data;
    }
    /**
     * @inheritDoc
     */
    public function get_fields()
    {
        return $this->fields;
    }
    /**
     * @inheritDoc
     */
    public function get_form_id()
    {
        return $this->form_id;
    }
    /**
     * @inheritDoc
     */
    public function get_normalized_data()
    {
        return $this->get_data();
    }
}
