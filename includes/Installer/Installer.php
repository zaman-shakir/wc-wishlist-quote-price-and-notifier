<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Installer;

use Shakir\WishlistQuotePriceAndNotifier\Logger as Logger;

/**
 * Class Installer
 * Does stuff when plugin is being installed
 */
class Installer
{
    /**
     * Run the installer
     * @return void
     */
    public function run()
    {
        $this->add_installed_flag();
        $this->add_version();
        $this->load_plugin_textdomain();
        $this->wqpn_create_wishlist_page();
    }

    public function wqpn_create_wishlist_page()
    {
        // Check if the page already exists
        $page = get_page_by_path('wqpn-my-wishlist');
        if ($page) {
            return; // Page already exists, no need to create it
        }

        // Create the page
        $wishlist_page = array(
            'post_title'    => 'My Wishlist',
            'post_content'  => '[wqpn_wishlist]', // Shortcode to display wishlist content
            'post_status'   => 'publish',
            'post_type'     => 'page',
        );
        wp_insert_post($wishlist_page);
    }

    public function add_installed_flag()
    {
        $installed = get_option('wc_wishlist_quote_and_price_notifier_installed');

        if (! $installed) {
            update_option('wc_wishlist_quote_and_price_notifier_installed', strtotime("now"));
        }
    }
    public function add_version()
    {
        update_option('wc_wishlist_quote_and_price_notifier_version', WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_VERSION);
    }
    /**
     * Load plugin text domain
     * @return void
     */
    public function load_plugin_textdomain()
    {
        //Logger::write_log('heee loggs');
        // Logger::get_instance()->write_log('heee loggs', true);

        load_plugin_textdomain(
            'wc_wishlist_quote_and_price_notifier',
            false,
            WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_URL . '/languages/'
        );

    }
}
