<?php
/**
 * Interface SpecialAction
 *
 * @package WPDesk\FS\TableRate\Rule\SpecialAction
 */

namespace WPDesk\FS\TableRate\Rule\SpecialAction;

/**
 * Special action interface.
 */
interface SpecialAction {

	/**
	 * @return string
	 */
	public function get_special_action_id();

	/**
	 * @return string
	 */
	public function get_name();

	/**
	 * @return string
	 */
	public function get_description();

	/**
	 * @return bool
	 */
	public function is_cancel();

	/**
	 * @return bool
	 */
	public function is_stop();

}
