<?php

/**
 * Array logger.
 *
 * @package WPDesk\FS\TableRate\Logger
 */
namespace FSVendor\WPDesk\FS\TableRate\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use FSVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use FSVendor\WPDesk\View\Resolver\ChainResolver;
use FSVendor\WPDesk\View\Resolver\DirResolver;
/**
 * Can log to array.
 */
class ArrayLogger implements \Psr\Log\LoggerInterface
{
    use LoggerTrait;
    /**
     * @var array
     */
    private $messages = array();
    /**
     * @param mixed $level .
     * @param string $message .
     * @param array $context .
     */
    public function log($level, $message, array $context = array())
    {
        $this->messages[] = array('level' => $level, 'message' => $message, 'context' => $context);
    }
    /**
     * @return array
     */
    public function get_messages()
    {
        return $this->messages;
    }
}
