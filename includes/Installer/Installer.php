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
        $this->create_wishlist_table();
    }
    public function create_wishlist_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wqpn_wishlist';

        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                wishlist_price float NOT NULL,
                quote_price float NOT NULL,
                products longtext NOT NULL,
                submitted_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                status varchar(20) NOT NULL,
                ip_address varchar(100) NOT NULL,
                unique_id varchar(36) NOT NULL,
                email varchar(100) NOT NULL,
                whatsapp varchar(20) DEFAULT NULL,
                telegram varchar(50) DEFAULT NULL,
                archived TINYINT(1) DEFAULT 0,
                used TINYINT(1) DEFAULT 0,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    public function wqpn_create_wishlist_page()
    {
        // Check if the page already exists
        $page = get_page_by_path('my-wishlist');
        if ($page) {
            return; // Page already exists, no need to create it
        }

        //todo Page name and slug can be created from admin settings
        $wishlist_page = array(
            'post_title'    => 'My Wishlist',
            'post_content'  => '[wqpn_wishlist]', // Shortcode to display wishlist content
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'wqpn-my-wishlist',
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
