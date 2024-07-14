<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

use Shakir\WishlistQuotePriceAndNotifier\Frontend\WishlistButtonHandler;
use Shakir\WishlistQuotePriceAndNotifier\Logger;

class WqpnHooks
{
    protected $logger;
    public function __construct()
    {
        //$this->logger = Logger::get_instance();
        //$this->logger = new Logger();
        $this->define_woocommerce_hooks();
        //$this->logger->write_log('Hooks enabled', true);

    }

    public function define_woocommerce_hooks()
    {
        add_action('wp_ajax_click_wishlist_button', [WishlistButtonHandler::class, 'wc_ajax_click_wishlist_button']);
        add_action('wp_ajax_nopriv_click_wishlist_button', [WishlistButtonHandler::class, 'wc_ajax_click_wishlist_button']);

        //add_action('wc_ajax_click_wishlist_button', [ WishlistButtonHandler::class, 'wc_ajax_click_wishlist_button' ]);
        //add_action( 'wp_ajax_triplea_orderpay_payment_request', TripleA_Payment_Gateway::class, 'triplea_orderpay_payment_request' );
        //add_action( 'wp_ajax_nopriv_triplea_orderpay_payment_request', TripleA_Payment_Gateway::class, 'triplea_orderpay_payment_request' );
    }
}
