<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Assets;

/**
 * Loads plugin assets
 */
class Assets
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [ $this, 'register_assets' ]);
        add_action('admin_enqueue_scripts', [ $this, 'register_assets' ]);
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
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;

            wp_register_style($handle, $style['src'], $deps, $style['version']);
        }
    }
    /**
     * All available scripts
     *
     * @return array
     */
    public function get_scripts()
    {
        return [
            'wctriplea-checkout-script' => [
                'src'     => WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_ASSETS . '/js/cart.js',
                'version' => filemtime(WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_PATH . '/assets/js/cart.js'),
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
            'wctriplea-admin-style' => [
                'src'     => WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_ASSETS . '/css/admin.css',
                'version' => filemtime(WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_PATH . '/assets/css/admin.css'),
            ],
            'wctriplea-checkout-style' => [
                'src'     => WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_ASSETS . '/css/cart.css',
                'version' => filemtime(WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_PATH . '/assets/css/cart.css'),
            ]
        ];
    }

}
