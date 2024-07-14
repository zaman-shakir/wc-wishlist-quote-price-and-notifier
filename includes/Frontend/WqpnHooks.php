<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

use Shakir\WishlistQuotePriceAndNotifier\Frontend\WishlistButtonHandler;
use Shakir\WishlistQuotePriceAndNotifier\Logger;

class WqpnHooks
{
    protected $logger;
    public function __construct()
    {
        $this->logger = Logger::get_instance();
        //$this->logger = new Logger();
        $this->define_woocommerce_hooks();
        $this->logger->write_log('Hooks enabled', true);

    }

    public function define_woocommerce_hooks()
    {

        add_action('wc_ajax_click_wishlist_button', [ WishlistButtonHandler::class, 'wc_ajax_click_wishlist_button' ]);

    }
}
