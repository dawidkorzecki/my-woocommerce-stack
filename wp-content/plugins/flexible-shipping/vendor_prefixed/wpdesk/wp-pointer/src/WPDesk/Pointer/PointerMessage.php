<?php

namespace FSVendor\WPDesk\Pointer;

/**
 * WordPress admin pointer message.
 *
 * @package WPDesk\Pointer
 */
class PointerMessage
{
    const USER_META_DISMISSED_WP_POINTERS = 'dismissed_wp_pointers';
    /**
     * Is action added?
     * @var bool
     */
    private $actionAdded = \false;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $anchor;
    /**
     * @var string
     */
    private $content;
    /**
     * @var string
     */
    private $title;
    /**
     * @var PointerPosition
     */
    private $position;
    /**
     * @var string|array
     */
    private $pointerClass;
    /**
     * @var int
     */
    private $pointerWidth;
    /**
     * @var PointerConditions
     */
    private $conditions;
    /**
     * @var array
     */
    private $pointerCss = array();
    /**
     * @var array
     */
    private $defaultPointerCss = array('top' => '50%', 'left' => '100%', '-webkit-transform' => 'translateY(-50%)', '-ms-transform' => 'translateY(-50%)', 'transform' => 'translateY(-50%)');
    /**
     * PointerMessage constructor.
     *
     * @param string $id
     * @param string $anchor
     * @param string $title
     * @param string $content
     * @param PointerPosition $position
     * @param string pointerClass
     * @param int $pointerWidth
     * @param null|PointerConditions $conditions Pointer conditions.
     * @param array $pointerCss Pointer CSS.
     */
    public function __construct($id, $anchor, $title, $content, $position = null, $pointerClass = 'wp-pointer', $pointerWidth = 320, $conditions = null, $pointerCss = array())
    {
        $this->id = $id;
        $this->anchor = $anchor;
        $this->title = $title;
        $this->content = $content;
        if ($position === null) {
            $position = new \FSVendor\WPDesk\Pointer\PointerPosition();
        }
        $this->position = $position;
        $this->pointerClass = $pointerClass;
        $this->pointerWidth = $pointerWidth;
        if (null === $conditions) {
            $this->conditions = new \FSVendor\WPDesk\Pointer\PointerConditions();
        } else {
            $this->conditions = $conditions;
        }
        $this->pointerCss = $pointerCss;
        $this->addAction();
    }
    /**
     * Enqueue scripts.
     */
    public function enqueueScripts()
    {
        \wp_enqueue_style('wp-pointer');
        \wp_enqueue_script('wp-pointer');
    }
    /**
     * Add notice action.
     */
    protected function addAction()
    {
        if (!$this->actionAdded) {
            \add_action('admin_print_footer_scripts', array($this, 'maybeRenderJavascript'));
            \add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
            $this->actionAdded = \true;
        }
    }
    /**
     * Remove action.
     */
    public function removeAction()
    {
        if ($this->actionAdded) {
            \remove_action('admin_print_footer_scripts', array($this, 'maybeRenderJavascript'));
            \remove_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
            $this->actionAdded = \false;
        }
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return string
     */
    public function getAnchor()
    {
        return $this->anchor;
    }
    /**
     * @param string $anchor
     */
    public function setAnchor($anchor)
    {
        $this->anchor = $anchor;
    }
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /**
     * @return array|string
     */
    public function getPosition()
    {
        return $this->position;
    }
    /**
     * @param array|string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
    /**
     * @return array|string
     */
    public function getPointerClass()
    {
        return $this->pointerClass;
    }
    /**
     * @param array|string $pointerClass
     */
    public function setPointerClass($pointerClass)
    {
        $this->pointerClass = $pointerClass;
    }
    /**
     * @return int
     */
    public function getPointerWidth()
    {
        return $this->pointerWidth;
    }
    /**
     * @param int $pointerWidth
     */
    public function setPointerWidth($pointerWidth)
    {
        $this->pointerWidth = $pointerWidth;
    }
    /**
     * @return PointerConditions
     */
    public function getConditions()
    {
        return $this->conditions;
    }
    /**
     * @return array
     */
    public function getPointerCss()
    {
        return $this->pointerCss;
    }
    /**
     * @param PointerConditions $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }
    /**
     * Render Java Script for pointer message.
     */
    public function renderJavaScript()
    {
        $pointerAnchor = $this->getAnchor();
        $pointerClass = $this->getPointerClass();
        $pointerContentId = 'wpdesk_pointer_content_' . $this->getId();
        $pointerWidth = $this->getPointerWidth();
        $pointerContent = \sprintf('<h3>%1$s</h3><p id="%2$s">%3$s</p>', $this->title, $pointerContentId, $this->content);
        $pointerPosition = $this->getPosition();
        $pointerId = $this->getId();
        $pointerCss = \array_merge($this->defaultPointerCss, $this->getPointerCss());
        include 'views/html-script-pointer-message.php';
    }
    /**
     * Meybe render Java Script for pointer message.
     */
    public function maybeRenderJavascript()
    {
        if ($this->conditions->areConditionsMet() && !$this->isDismissed()) {
            $this->renderJavaScript();
        }
    }
    /**
     * Is pointer message already dismissed?
     *
     * @return bool
     */
    private function isDismissed()
    {
        $dismissedPointerMessages = \array_filter(\explode(',', (string) \get_user_meta(\get_current_user_id(), self::USER_META_DISMISSED_WP_POINTERS, \true)));
        return \in_array($this->id, $dismissedPointerMessages, \true);
    }
    /**
     * Un dismiss pointer message.
     */
    public function unDismiss()
    {
        $dismissedPointerMessages = \array_filter(\explode(',', (string) \get_user_meta(\get_current_user_id(), self::USER_META_DISMISSED_WP_POINTERS, \true)));
        foreach ($dismissedPointerMessages as $key => $value) {
            if ($value === $this->getId()) {
                unset($dismissedPointerMessages[$key]);
                \update_user_meta(\get_current_user_id(), self::USER_META_DISMISSED_WP_POINTERS, \implode(',', $dismissedPointerMessages));
            }
        }
    }
}
