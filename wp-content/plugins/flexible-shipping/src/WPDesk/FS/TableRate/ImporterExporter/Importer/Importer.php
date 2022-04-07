<?php
/**
 * Interface Importer
 *
 * @package WPDesk\FS\TableRate\Importer
 */

namespace WPDesk\FS\TableRate\ImporterExporter\Importer;

/**
 * Interface Importer
 *
 * @package WPDesk\FS\TableRate\ImporterExporter\Importer
 */
interface Importer {
	/**
	 * Process Importer.
	 */
	public function import();
}
