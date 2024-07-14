<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

use Shakir\WishlistQuotePriceAndNotifier\Logger;

class WishlistButtonHandler
{
    public function wc_ajax_click_wishlist_button()
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], '_wishlist_quote_price_notify')) {
            wp_die(__('Bad attempt, invalid nonce for new wishlist request', 'wc-triplea-crypto-payment'));
        }

        var_dump($_REQUEST);
        die();
        echo json_encode(
            [
                'status'            => 201,
                'message'           => 'Wishlist received successfully',

            ]
        );


    }



}
