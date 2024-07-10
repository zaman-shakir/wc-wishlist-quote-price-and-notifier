<?php
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

        load_plugin_textdomain(
            'wc_wishlist_quote_and_price_notifier',
            false,
            WC_WISHLIST_QUOTE_PRICE_AND_NOTIFIER_URL . '/languages/'
        );

    }
}
