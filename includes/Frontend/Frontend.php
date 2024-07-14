<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

use Shakir\WishlistQuotePriceAndNotifier\Frontend\WqpnHooks;
use Shakir\WishlistQuotePriceAndNotifier\Frontend\WishlistButton;
use Shakir\WishlistQuotePriceAndNotifier\Frontend\WishlistButtonHandler;
use Shakir\WishlistQuotePriceAndNotifier\Logger;

class Frontend
{
    protected $logger;
    protected static $instance = null;

    private function __construct()
    {
        $this->logger = Logger::get_instance();
        $this->logger->write_log('Frontend instance created', true);
        $this->load_frontend_class();
    }

    /**
     * Get the singleton instance of the Frontend class.
     *
     * @return Frontend
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        } else {
            Logger::get_instance()->write_log('Frontend instance reused', true);
        }
        return self::$instance;
    }

    protected function load_frontend_class()
    {
        $this->logger->write_log('Frontend enabled', true);
        // new WqpnHooks();
        // new WishlistButton();
        // new WishlistButtonHandler();
    }
}
