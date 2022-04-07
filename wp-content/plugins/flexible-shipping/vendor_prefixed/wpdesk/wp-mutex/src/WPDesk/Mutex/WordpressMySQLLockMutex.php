<?php

namespace FSVendor\WPDesk\Mutex;

class WordpressMySQLLockMutex implements \FSVendor\WPDesk\Mutex\Mutex
{
    use WordpressWpdb;
    /** @var string Name of the resource to lock */
    private $lockName;
    /** @var int Wait for lock timeout in seconds */
    private $waitForLockTimeout;
    /**
     * Wordpress_Post_Mutex constructor.
     *
     * @param string $lockName Name of the resource to lock
     * @param int $waitForLockTimeout Wait for lock timeout in seconds
     */
    public function __construct($lockName = '_mutex', $waitForLockTimeout = 5)
    {
        $this->wpdb = $this->getWpdbFromGlobal();
        $this->lockName = $this->wpdb->_real_escape($lockName);
        $this->waitForLockTimeout = \intval($waitForLockTimeout);
    }
    /**
     * Factory method
     *
     * @param \WC_Order $order Order for which mutex will be prepared
     * @param string $lockName Name of the resource to lock
     * @param int $waitForLockTimeout Lock timeout in seconds
     *
     * @return WordpressMySQLLockMutex
     */
    public static function fromOrder(\WC_Order $order, $lockName = '_mutex', $waitForLockTimeout = 5)
    {
        return new self('order' . \strval($order->get_id()) . $lockName, $waitForLockTimeout);
    }
    /**
     * Tries to set lock and returns true if successful
     *
     * @return bool
     */
    public function acquireLock()
    {
        $this->wpdb = $this->getWpdbFromGlobal();
        $lockRow = $this->wpdb->get_row($this->wpdb->prepare('SELECT GET_LOCK(%s,%d) as lock_set', array($this->lockName, $this->waitForLockTimeout)));
        return 1 === \intval($lockRow->lock_set);
    }
    /**
     * Releases all locks
     *
     * @return void
     */
    public function releaseLock()
    {
        $this->wpdb = $this->getWpdbFromGlobal();
        $this->wpdb->get_row($this->wpdb->prepare('SELECT RELEASE_LOCK(%s) as lock_released', array($this->lockName)));
    }
}
