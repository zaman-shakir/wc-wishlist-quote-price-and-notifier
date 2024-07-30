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
        //$this->logger = Logger::get_instance();
        //$this->logger = new Logger();
        $this->define_woocommerce_hooks();
        //$this->logger->write_log('Hooks enabled', true);

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

        //add_action('wc_ajax_click_wishlist_button', [ WishlistButtonHandler::class, 'wc_ajax_click_wishlist_button' ]);
        //add_action( 'wp_ajax_triplea_orderpay_payment_request', TripleA_Payment_Gateway::class, 'triplea_orderpay_payment_request' );
        //add_action( 'wp_ajax_nopriv_triplea_orderpay_payment_request', TripleA_Payment_Gateway::class, 'triplea_orderpay_payment_request' );
    }
    ### Handling the Form Submission in PHP


    // public function handle_submit_wishlist_price()
    // {
    //     // Verify nonce and user permissions if necessary

    //     // Retrieve and sanitize form data
    //     $price = sanitize_text_field($_POST['price']);
    //     $email = sanitize_email($_POST['email']);
    //     $whatsapp = sanitize_text_field($_POST['whatsapp']);
    //     $telegram = sanitize_text_field($_POST['telegram']);

    //     // Process the data (e.g., save to database, send email, etc.)

    //     // Send a response back to the AJAX request
    //     wp_send_json_success(array(
    //         'message' => 'Price submitted successfully!'
    //     ));
    // }
    //add_action('wp_ajax_submit_wishlist_price', 'handle_submit_wishlist_price');
    //add_action('wp_ajax_nopriv_submit_wishlist_price', 'handle_submit_wishlist_price');
}
