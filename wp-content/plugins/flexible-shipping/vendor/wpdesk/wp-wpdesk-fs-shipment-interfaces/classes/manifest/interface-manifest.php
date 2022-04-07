<?php

interface WPDesk_Flexible_Shipping_Manifest_Interface  {

    /**
     * @return array
     * Returns manifest data in array
     *      file_name => file name for manifest
     *      content   => pdf content
     */
    public function get_manifest();

    /**
     * @return string
     * Returns manifest number
     */
    public function get_number();

    /**
     * @return null
     * Generates manifest (ie. in API)
     */
    public function generate();

    /**
     * @return null
     * Cancels manifest (ie. in API)
     */
    public function cancel();

}
