<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Assets;

use Shakir\WishlistQuotePriceAndNotifier\Logger;

/**
 * Loads plugin assets
 */
class Assets
{
    protected $logger;
    /**
     * Class constructor
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [ $this, 'register_assets' ]);
        add_action('admin_enqueue_scripts', [ $this, 'register_assets' ]);
        $this->logger = Logger::get_instance();
        $this->logger->write_log("Plugin assets loaded", true);
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets()
    {
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;

            wp_register_script($handle, $script['src'], $deps, $script['version'], true);
            wp_enqueue_script($handle); // Enqueue the script

        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;

            wp_register_style($handle, $style['src'], $deps, $style['version']);
            wp_enqueue_style($handle); // Enqueue the style

        }
        // Enqueue Dashicons
        //wp_enqueue_style('dashicons');
    }
    /**
     * All available scripts
     *
     * @return array
     */
    public function get_scripts()
    {
        return [
            'wqpn-checkout-script' => [
                'src'     => WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_ASSETS . '/js/wishlist.js',
                'version' => filemtime(WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_PATH . '/assets/js/wishlist.js'),
                'deps'    => [ 'jquery' ]
            ],
        ];
    }

    /**
     * All available styles
     *
     * @return array
     */
    public function get_styles()
    {
        return [
            'wqpn-admin-style' => [
                'src'     => WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_ASSETS . '/css/admin.css',
                'version' => filemtime(WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_PATH . '/assets/css/admin.css'),
            ],
            'wqpn-checkout-style' => [
                'src'     => WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_ASSETS . '/css/cart.css',
                'version' => filemtime(WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_PATH . '/assets/css/cart.css'),
            ]
        ];
    }

}
