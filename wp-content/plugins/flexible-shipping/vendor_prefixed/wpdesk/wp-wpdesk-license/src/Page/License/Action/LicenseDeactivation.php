<?php

namespace FSVendor\WPDesk\License\Page\License\Action;

use FSVendor\WPDesk\License\Page\Action;
/**
 * Can deactivate plugin license.
 *
 * @package WPDesk\License\Page\License\Action
 */
class LicenseDeactivation extends \FSVendor\WPDesk\License\Page\License\Action\AbstractAction implements \FSVendor\WPDesk\License\Page\Action
{
    /**
     * Deactivate plugin subscription.
     *
     * @param $plugin array Info about plugin
     */
    public function execute(array $plugin)
    {
        $args = ['email' => $plugin['api_manager']->options[$plugin['api_manager']->activation_email], 'licence_key' => $plugin['api_manager']->options[$plugin['api_manager']->api_key]];
        $activate_results = \json_decode($plugin['api_manager']->key()->deactivate($args), \true);
        // Used to display results for development
        //print_r($activate_results); exit();
        $deactivated = \false;
        if ($activate_results['deactivated'] === \true) {
            $update = [$plugin['api_manager']->api_key => '', $plugin['api_manager']->activation_email => ''];
            $merge_options = \array_merge($plugin['api_manager']->options, $update);
            \update_option($plugin['api_manager']->data_key, $merge_options);
            \update_option($plugin['api_manager']->activated_key, 'Deactivated');
            \delete_option($plugin['api_manager']->upgrade_url_key);
            $this->add_error('wc_am_deactivate_text', 'deactivate_msg', \__('Plugin license deactivated. ', 'flexible-shipping') . "{$activate_results['activations_remaining']}.", 'updated');
            $deactivated = \true;
            $plugin_wpdesk_name = $plugin['plugin'];
            $plugin_product_id = $plugin['product_id'];
            \do_action('wpdesk_subscription_deactivated', $plugin_wpdesk_name, $plugin_product_id);
        }
        if (!$deactivated && isset($activate_results['code'])) {
            switch ($activate_results['code']) {
                case '100':
                    $this->add_error('api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                    $options[$plugin['api_manager']->activation_email] = '';
                    $options[$plugin['api_manager']->api_key] = '';
                    \update_option($plugin['api_manager']->data_key, $plugin['api_manager']->options);
                    \update_option($plugin['api_manager']->activated_key, 'Deactivated');
                    break;
                case '101':
                    $this->add_error('api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                    $options[$plugin['api_manager']->api_key] = '';
                    $options[$plugin['api_manager']->activation_email] = '';
                    \update_option($plugin['api_manager']->data_key, $plugin['api_manager']->options);
                    \update_option($plugin['api_manager']->activated_key, 'Deactivated');
                    break;
                case '102':
                    $this->add_error('api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                    $options[$plugin['api_manager']->api_key] = '';
                    $options[$plugin['api_manager']->activation_email] = '';
                    \update_option($plugin['api_manager']->data_key, $plugin['api_manager']->options);
                    \update_option($plugin['api_manager']->activated_key, 'Deactivated');
                    break;
                case '103':
                    $this->add_error('api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                    $options[$plugin['api_manager']->api_key] = '';
                    $options[$plugin['api_manager']->activation_email] = '';
                    \update_option($plugin['api_manager']->data_key, $plugin['api_manager']->options);
                    \update_option($plugin['api_manager']->activated_key, 'Deactivated');
                    break;
                case '104':
                    $this->add_error('api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                    $options[$plugin['api_manager']->api_key] = '';
                    $options[$plugin['api_manager']->activation_email] = '';
                    \update_option($plugin['api_manager']->data_key, $plugin['api_manager']->options);
                    \update_option($plugin['api_manager']->activated_key, 'Deactivated');
                    break;
                case '105':
                    $this->add_error('api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                    $options[$plugin['api_manager']->api_key] = '';
                    $options[$plugin['api_manager']->activation_email] = '';
                    \update_option($plugin['api_manager']->data_key, $plugin['api_manager']->options);
                    \update_option($plugin['api_manager']->activated_key, 'Deactivated');
                    break;
                case '106':
                    $this->add_error('sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                    $options[$plugin['api_manager']->api_key] = '';
                    $options[$plugin['api_manager']->activation_email] = '';
                    \update_option($plugin['api_manager']->data_key, $plugin['api_manager']->options);
                    \update_option($plugin['api_manager']->activated_key, 'Deactivated');
                    break;
            }
        }
    }
}
