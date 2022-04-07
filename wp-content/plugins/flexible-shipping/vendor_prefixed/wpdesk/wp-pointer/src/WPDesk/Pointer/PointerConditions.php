<?php

namespace FSVendor\WPDesk\Pointer;

/**
 * Pointer conditions.
 *
 * @package WPDesk\Pointer
 */
class PointerConditions
{
    /**
     * @var null|string|array
     */
    private $screenId;
    /**
     * @var null|string
     */
    private $capability;
    /**
     * PointerConditions constructor.
     *
     * @param null|string|array $screenId Screen id. null or empty string - all screens.
     * @param null|string $capability User capability. null or empty string for all capabilities.
     */
    public function __construct($screenId = null, $capability = null)
    {
        $this->screenId = $screenId;
        $this->capability = $capability;
    }
    /**
     * @return array|string|null
     */
    public function getScreenId()
    {
        return $this->screenId;
    }
    /**
     * @param array|string|null $screenId
     */
    public function setScreenId($screenId)
    {
        $this->screenId = $screenId;
    }
    /**
     * @return string
     */
    public function getCapability()
    {
        return $this->capability;
    }
    /**
     * @param string $capability
     */
    public function setCapability($capability)
    {
        $this->capability = $capability;
    }
    /**
     * @return bool
     */
    private function areScreenIdMet()
    {
        $screenIdMet = \false;
        if (!empty($this->screenId)) {
            if (!\is_array($this->screenId)) {
                $this->screenId = array($this->screenId);
            }
            $screen = \get_current_screen();
            if (null !== $screen && \in_array($screen->id, $this->screenId, \true)) {
                $screenIdMet = \true;
            }
        } else {
            $screenIdMet = \true;
        }
        return $screenIdMet;
    }
    /**
     * @return bool
     */
    private function areCapabilityMet()
    {
        if (!empty($this->capability)) {
            $capabilityMet = \current_user_can($this->capability);
        } else {
            $capabilityMet = \true;
        }
        return $capabilityMet;
    }
    /**
     * @return bool
     */
    public function areConditionsMet()
    {
        return $this->areCapabilityMet() && $this->areScreenIdMet();
    }
}
