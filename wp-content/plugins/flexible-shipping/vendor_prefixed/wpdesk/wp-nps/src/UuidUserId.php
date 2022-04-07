<?php

namespace FSVendor\WPDesk\Nps;

/**
 * Provides UUID as user ID.
 */
class UuidUserId implements \FSVendor\WPDesk\Nps\UserId
{
    /**
     * @var string
     */
    private $meta_name;
    /**
     * UserId constructor.
     *
     * @param \WP_User $current_user
     * @param string $meta_name
     */
    public function __construct($meta_name = 'wpdesk_id')
    {
        $this->meta_name = $meta_name;
    }
    /**
     * @return string
     */
    public function get_id()
    {
        $current_user = \wp_get_current_user();
        if ($current_user) {
            $user_id = \get_user_meta($current_user->ID, $this->meta_name, \true);
            if (!$user_id || !\wp_is_uuid($user_id)) {
                $user_id = \wp_generate_uuid4();
                \add_user_meta($current_user->ID, $this->meta_name, $user_id, \true);
            }
        } else {
            $user_id = 'Anonymous';
        }
        return $user_id;
    }
}
