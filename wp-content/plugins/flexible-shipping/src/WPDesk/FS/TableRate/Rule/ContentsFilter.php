<?php
/**
 * Interface ContentsFilter
 *
 * @package WPDesk\FS\TableRate\Rule
 */

namespace WPDesk\FS\TableRate\Rule;

/**
 * Contents filter interface.
 */
interface ContentsFilter {

	/**
	 * Returns filtered contents.
	 *
	 * @param array $contents .
	 *
	 * @return array
	 */
	public function get_filtered_contents( array $contents );

}
