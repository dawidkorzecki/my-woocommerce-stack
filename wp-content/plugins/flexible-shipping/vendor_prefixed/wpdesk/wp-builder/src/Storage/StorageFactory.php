<?php

namespace FSVendor\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \FSVendor\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}
