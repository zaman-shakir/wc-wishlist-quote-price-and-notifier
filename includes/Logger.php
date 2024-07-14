<?php

namespace Shakir\WishlistQuotePriceAndNotifier;

class Logger
{
    /**
     * @var Logger
     */
    private static $instance;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Get the singleton instance of the Logger.
     *
     * @return Logger
     */
    public static function get_instance()
    {
        if (! self::$instance) {

            var_dump("new logger created");
            self::$instance = new self();
        }
        var_dump("old logger returned");
        return self::$instance;
    }

    /**
     * Wishlist Quote Price and Notifier Logger to log all the debugs.
     *
     * @param string $log The log message.
     * @param bool $log_enabled Flag to enable or disable logging.
     * @param string $level The log level ('info', 'warning', 'error').
     */
    public function write_log($log, $log_enabled = false, $level = 'info')
    {
        if ($log_enabled) {
            $logger = wc_get_logger();
            $context = [ 'source' => 'wc-wishlist-quote-and-price-notifier' ];

            switch ($level) {
                case 'warning':
                    $logger->warning($log, $context);
                    break;
                case 'error':
                    $logger->error($log, $context);
                    break;
                case 'info':
                default:
                    $logger->info($log, $context);
                    break;
            }
        }
    }
}
