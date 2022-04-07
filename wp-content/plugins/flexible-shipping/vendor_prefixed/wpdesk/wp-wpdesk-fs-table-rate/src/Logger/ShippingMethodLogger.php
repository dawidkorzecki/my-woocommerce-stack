<?php

/**
 * Shipping method logger.
 *
 * @package WPDesk\FS\TableRate\Logger
 */
namespace FSVendor\WPDesk\FS\TableRate\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
/**
 * Can log shipping method messages.
 */
class ShippingMethodLogger implements \Psr\Log\LoggerInterface
{
    use LoggerTrait;
    /**
     * @var LoggerInterface
     */
    private $fs_logger;
    /**
     * @var LoggerInterface
     */
    private $notice_logger;
    /**
     * ShippingMethodLogger constructor.
     *
     * @param LoggerInterface $fs_logger
     * @param NoticeLogger $notice_logger
     */
    public function __construct(\Psr\Log\LoggerInterface $fs_logger, \Psr\Log\LoggerInterface $notice_logger)
    {
        $this->fs_logger = $fs_logger;
        $this->notice_logger = $notice_logger;
    }
    /**
     * @param mixed $level .
     * @param string $message .
     * @param array $context .
     */
    public function log($level, $message, array $context = array())
    {
        $this->fs_logger->log($level, $message, $context);
        $this->notice_logger->log($level, $message, $context);
    }
    /**
     * Log entries from array logger.
     *
     * @param ArrayLogger $array_logger
     * @param array $context
     */
    public function log_from_array_logger(\FSVendor\WPDesk\FS\TableRate\Logger\ArrayLogger $array_logger, array $context = array())
    {
        foreach ($array_logger->get_messages() as $message) {
            $this->log($message['level'], $message['message'], \array_merge($message['context'], $context));
        }
    }
    /**
     * Show notice if enabled.
     */
    public function show_notice_if_enabled()
    {
        $this->notice_logger->show_notice_if_enabled();
    }
    /**
     * @return array
     */
    public function get_configuration_section_context()
    {
        return array('section' => \__('shipping method configuration', 'flexible-shipping'));
    }
    /**
     * @return array
     */
    public function get_input_data_context()
    {
        return array('section' => \__('input data', 'flexible-shipping'));
    }
    /**
     * @return array
     */
    public function get_rule_context($rule_triggered)
    {
        return array('section' => \sprintf(\__('rules (%1$s)', 'flexible-shipping'), $rule_triggered ? \__('triggered', 'flexible-shipping') : \__('not triggered', 'flexible-shipping')));
    }
    /**
     * @return array
     */
    public function get_results_context()
    {
        return array('section' => \__('the result of shipping method\'s usage', 'flexible-shipping'));
    }
}
