<?php

namespace FSVendor\WPDesk\FS\Shipment\RestApi;

/**
 * Data providers.
 * Collects data providers and can return provider per integration or default provider.
 */
class OrderDataProvidersCollection
{
    /**
     * Providers.
     *
     * @var OrderDataProvider[]
     */
    private $providers = array();
    /**
     * Add provider.
     *
     * @param string $integration .
     * @param OrderDataProvider $provider .
     */
    public function set_provider($integration, \FSVendor\WPDesk\FS\Shipment\RestApi\OrderDataProvider $provider)
    {
        $this->providers[$integration] = $provider;
    }
    /**
     * Get provider for integration.
     *
     * @param string $integration .
     *
     * @return OrderDataProvider
     */
    public function get_provider_for_integration($integration)
    {
        if (isset($this->providers[$integration])) {
            return $this->providers[$integration];
        }
        return new \FSVendor\WPDesk\FS\Shipment\RestApi\OrderDataProviderDefault();
    }
}
