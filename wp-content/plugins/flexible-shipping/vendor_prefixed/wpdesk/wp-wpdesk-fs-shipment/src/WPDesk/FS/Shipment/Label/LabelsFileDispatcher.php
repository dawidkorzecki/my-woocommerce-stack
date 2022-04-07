<?php

/**
 * Labels file dispatcher.
 *
 * @package Flexible Shipping
 */
namespace FSVendor\WPDesk\FS\Shipment\Label;

/**
 * Can dispatch file to browser.
 */
class LabelsFileDispatcher
{
    /**
     * Dispatches and delete temporary labels file.
     *
     * @param string $file_name Filename to send to browser.
     * @param string $tmp_file_path Temporary labels file name.
     */
    public function dispatch_and_delete_labels_file($file_name, $tmp_file_path)
    {
        \header('Content-Description: File Transfer');
        \header('Content-Type: application/octet-stream');
        \header('Content-Disposition: attachment; filename="' . \basename($file_name) . '"');
        \header('Expires: 0');
        \header('Cache-Control: must-revalidate');
        \header('Pragma: public');
        \header('Content-Length: ' . \filesize($tmp_file_path));
        \readfile($tmp_file_path);
        // phpcs:ignore
        \unlink($tmp_file_path);
    }
}
