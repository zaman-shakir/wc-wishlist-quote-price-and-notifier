<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Admin;
// testing10 eleas
/**
 * Class Admin_Notice
 * Handles admin notices for required plugins.
 */
class Admin_Notice
{
    /**
     * Print notices if required plugins are not installed or active
     * @return void
     */
    public function check_require_plugin_notice()
    {

        $wc_title = __('WooCommerce', 'wc-wishlist-quote-and-price-notifier');
        $wc_url   = 'https://wordpress.org/plugins/woocommerce/';

        $notice = sprintf(
            /* translators: 1: Plugin name 2: WC title & installation link 3: WCS title & installation link */
            __('%1$s requires %2$s & %3$s to be installed and activated to function properly.', 'wc-wishlist-quote-and-price-notifier'),
            '<strong>' . __('Wishlist Quote Price and Notifier for Store', 'wc-wishlist-quote-and-price-notifier') . '</strong>',
            '<a href="' . esc_url($wc_url) . '" target="_blank">' . $wc_title . '</a>',
            '<a href="' . esc_url($wc_url) . '" target="_blank">' . $wc_title . '</a>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice);
    }
}
