<?php
/**
 * Class None
 *
 * @package WPDesk\FS\TableRate\Rule\SpecialAction
 */

namespace WPDesk\FS\TableRate\Rule\SpecialAction;

/**
 * None special action.
 */
class None extends AbstractSpecialAction {

	/**
	 * None constructor.
	 */
	public function __construct() {
		parent::__construct( 'none', __( 'None', 'flexible-shipping' ) );
	}

	/**
	 * @inheritDoc
	 */
	public function is_cancel() {
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function is_stop() {
		return false;
	}
}
