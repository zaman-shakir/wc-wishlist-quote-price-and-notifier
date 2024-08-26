<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

use Shakir\WishlistQuotePriceAndNotifier\Frontend\WishlistButtonHandler;
use Shakir\WishlistQuotePriceAndNotifier\Frontend\WishlistPage;

use Shakir\WishlistQuotePriceAndNotifier\Logger;

class WqpnHooks
{
    protected $logger;
    public function __construct()
    {
        $this->define_woocommerce_hooks();

    }

    public function define_woocommerce_hooks()
    {
        add_action('wp_ajax_click_wishlist_button', [WishlistButtonHandler::class, 'wc_ajax_click_wishlist_button']);
        add_action('wp_ajax_nopriv_click_wishlist_button', [WishlistButtonHandler::class, 'wc_ajax_click_wishlist_button']);

        add_action('wp_ajax_is_user_logged_in', [WishlistPage::class, 'wqpn_is_user_logged_in']);
        add_action('wp_ajax_nopriv_is_user_logged_in', [WishlistPage::class, 'wqpn_is_user_logged_in']);


        add_action('admin_post_wqpn_submit_price', [WishlistPage::class, 'handle_wishlist_form_submission']);
        add_action('admin_post_nopriv_wqpn_submit_price', [WishlistPage::class, 'handle_wishlist_form_submission']);

        add_action('admin_post_wqpn_submit_quote_price', [WishlistPage::class, 'handle_wqpn_submit_quote_price']);
        add_action('admin_post_nopriv_wqpn_submit_quote_price', [WishlistPage::class, 'handle_wqpn_submit_quote_price']);

    }
}
