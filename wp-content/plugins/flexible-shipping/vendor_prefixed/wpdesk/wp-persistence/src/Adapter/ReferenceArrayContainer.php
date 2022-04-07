<?php

namespace FSVendor\WPDesk\Persistence\Adapter;

/**
 * Container that uses array as a persistent memory. When the value in container is changed, the value in
 * given array is also changed using reference.
 *
 * @package WPDesk\Persistence
 */
final class ReferenceArrayContainer extends \FSVendor\WPDesk\Persistence\Adapter\ArrayContainer
{
    /**
     * @param array $referenced You have to pass this array. It can not be value.
     */
    public function __construct(array &$referenced)
    {
        $this->array =& $referenced;
    }
}
