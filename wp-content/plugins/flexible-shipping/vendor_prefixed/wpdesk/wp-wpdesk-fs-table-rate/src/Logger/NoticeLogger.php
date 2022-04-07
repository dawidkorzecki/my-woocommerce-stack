<?php

/**
 * Notice logger.
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
 * Can log to WC Notice.
 */
class NoticeLogger implements \Psr\Log\LoggerInterface
{
    use LoggerTrait;
    /**
     * @var array
     */
    private $messages = array();
    /**
     * @var bool
     */
    private $notice_enabled;
    /**
     * @var string
     */
    private $shipping_method_title;
    /**
     * @var string
     */
    private $shipping_method_url;
    /**
     * NoticeLogger constructor.
     *
     * @param string $shipping_method_title .
     * @param string $shipping_method_url .
     * @param bool $notice_enabled .
     */
    public function __construct($shipping_method_title, $shipping_method_url, $notice_enabled)
    {
        $this->notice_enabled = $notice_enabled;
        $this->shipping_method_url = $shipping_method_url;
        $this->shipping_method_title = $shipping_method_title;
    }
    /**
     * @param mixed $level .
     * @param string $message .
     * @param array $context .
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->notice_enabled) {
            if (isset($context['section'])) {
                $context = $context['section'];
                if (!isset($this->messages[$context])) {
                    $this->messages[$context] = array();
                }
                $this->messages[$context][] = array('level' => $level, 'message' => $message, 'context' => $context);
            }
        }
    }
    /**
     * Show notice if logger is enabled.
     */
    public function show_notice_if_enabled()
    {
        if ($this->notice_enabled && \count($this->messages)) {
            $content = $this->prepare_notice_content();
            if (!\wc_has_notice($content, 'notice')) {
                \wc_add_notice($content, 'notice');
                /**
                 * Do actions when Flexible Shipping debug notice is added.
                 *
                 * @param string $content Notice content.
                 */
                \do_action('flexible_shipping_debug_notice_added', $content);
            }
        }
    }
    /**
     * Prepare notice content.
     *
     * @return string
     */
    private function prepare_notice_content()
    {
        $renderer = new \FSVendor\WPDesk\View\Renderer\SimplePhpRenderer(new \FSVendor\WPDesk\View\Resolver\DirResolver(__DIR__ . '/view'));
        $content = $renderer->render('display-notice-header', array('shipping_method_url' => $this->shipping_method_url, 'shipping_method_title' => $this->shipping_method_title));
        foreach ($this->messages as $section => $section_messages) {
            $section_content = $this->prepare_content_from_section_messages($section_messages);
            $content .= $renderer->render('display-notice-content-single-value', array('section' => $section, 'section_content' => $section_content));
        }
        $content .= $renderer->render('display-notice-footer');
        return $content;
    }
    /**
     * @param array $section_messages .
     *
     * @return string
     */
    private function prepare_content_from_section_messages(array $section_messages)
    {
        $content = '';
        foreach ($section_messages as $message) {
            $content .= $message['message'] . \PHP_EOL;
        }
        return \trim($content);
    }
}
