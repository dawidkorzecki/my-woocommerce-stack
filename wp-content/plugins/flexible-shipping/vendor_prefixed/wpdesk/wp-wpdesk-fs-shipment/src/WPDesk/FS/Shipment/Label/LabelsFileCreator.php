<?php

/**
 * Labels file creator.
 *
 * @package Flexible Shipping
 */
namespace FSVendor\WPDesk\FS\Shipment\Label;

/**
 * Can create labels file.
 * When there is one label file it creates file from this label.
 * When there is more than one label file it creates zip file with all labels.
 */
class LabelsFileCreator
{
    /**
     * Labels.
     *
     * @var array
     */
    private $labels = array();
    /**
     * File name.
     *
     * @var string
     */
    private $file_name;
    /**
     * Tmp file name.
     *
     * @var string
     */
    private $tmp_file_name;
    /**
     * .
     *
     * @param array $labels .
     * @see WPDesk_Flexible_Shipping_Integration_Label_Builder::get_labels_for_shipments()
     */
    public function __construct(array $labels)
    {
        $this->labels = $labels;
        $this->prepare_file_names();
    }
    /**
     * Create labels file.
     *
     * @throws \WPDesk_Flexible_Shipping_Unable_To_Create_Tmp_Zip_File_Exception .
     */
    public function create_labels_file()
    {
        if (1 === \count($this->labels)) {
            \file_put_contents($this->tmp_file_name, $this->labels[0]['content']);
            // phpcs:ignore
        } else {
            $zip = new \ZipArchive();
            if (!$zip->open($this->tmp_file_name, \ZipArchive::CREATE)) {
                throw new \FSVendor\WPDesk_Flexible_Shipping_Unable_To_Create_Tmp_Zip_File_Exception();
            }
            foreach ($this->labels as $label) {
                if (isset($label['content'])) {
                    $zip->addFromString($label['file_name'], $label['content']);
                }
            }
        }
    }
    /**
     * Prepare file names.
     *
     * @throws \WPDesk_Flexible_Shipping_Unable_To_Create_Tmp_File_Exception .
     */
    private function prepare_file_names()
    {
        if (1 === \count($this->labels)) {
            $this->file_name = $this->labels[0]['file_name'];
        } else {
            $this->file_name = 'labels.zip';
        }
        $this->tmp_file_name = @\tempnam('tmp', 'labels_');
        // phpcs:ignore
        if (!$this->tmp_file_name) {
            throw new \FSVendor\WPDesk_Flexible_Shipping_Unable_To_Create_Tmp_File_Exception();
        }
    }
    /**
     * Get file name.
     *
     * @return string
     */
    public function get_file_name()
    {
        return $this->file_name;
    }
    /**
     * Get temporary file name.
     * In this file we save labels.
     *
     * @return string
     */
    public function get_tmp_file_name()
    {
        return $this->tmp_file_name;
    }
}
