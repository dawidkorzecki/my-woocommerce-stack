<?php
/**
 * Class AbstractSpecialAction
 *
 * @package WPDesk\FS\TableRate\Rule\SpecialAction
 */

namespace WPDesk\FS\TableRate\Rule\SpecialAction;

/**
 * Abstract special action.
 */
abstract class AbstractSpecialAction implements SpecialAction {

	/**
	 * @var string
	 */
	private $special_action_id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * AbstractSpecialAction constructor.
	 *
	 * @param string $special_action_id .
	 * @param string $name .
	 * @param string $description .
	 */
	public function __construct( $special_action_id, $name, $description = '' ) {
		$this->special_action_id = $special_action_id;
		$this->name              = $name;
		$this->description       = $description;
	}

	/**
	 * @return string
	 */
	public function get_special_action_id() {
		return $this->special_action_id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @return bool
	 */
	abstract public function is_cancel();

	/**
	 * @return bool
	 */
	abstract public function is_stop();

}
