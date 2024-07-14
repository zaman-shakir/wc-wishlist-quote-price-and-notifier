<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

use Shakir\WishlistQuotePriceAndNotifier\Logger;

class WishlistButtonHandler
{
    public static function wc_ajax_click_wishlist_button()
    {

        echo json_encode(
            [
                'status'            => 201,
                'message'           => 'Wishlist received successfully',

            ]
        );
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], '_wishlist_quote_price_notify')) {
            wp_die(__('Bad attempt, invalid nonce for new wishlist request', 'wc-triplea-crypto-payment'));
        }


        echo json_encode(
            [
                'status'            => 201,
                'message'           => 'Wishlist received successfully',

            ]
        );


    }



}
