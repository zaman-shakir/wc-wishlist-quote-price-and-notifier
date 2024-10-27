<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

use Shakir\WishlistQuotePriceAndNotifier\Frontend\WishlistButtonHandler;

class WishlistButton
{
    public function __construct()
    {
        add_action('woocommerce_after_shop_loop_item', [$this, 'display_wishlist_button'], 20);
        add_action('woocommerce_before_add_to_cart_form', [$this, 'display_wishlist_button'], 35);
        add_action('wp_footer', [$this, 'display_wishlist_icon'], 40);
    }

    public function display_wishlist_button()
    {
        global $product;

        $product_id = $product->get_id();
        $is_wishlisted = $this->is_wishlisted($product_id);
        $icon = $is_wishlisted ? 'wqpn-wishlist-full' : 'wqpn-wishlist-empty';
        $nonce = wp_create_nonce('_wishlist_quote_price_notify');
        $url = esc_url(admin_url('admin-ajax.php'));
        $label = $is_wishlisted ? __('Remove from Wishlist', 'wishlist') : __('Add to Wishlist', 'wishlist');

        echo sprintf(
            '<div class="wqpn-wishlist"><span class="wqpn-wishlist-button %s" data-product-id="%d" data-nonce="%s" data-url="%s"> %s</span></div>',
            esc_attr($icon),
            esc_attr($product_id),
            esc_attr($nonce),
            esc_attr($url),
            esc_html($label)
        );
    }
    private function is_wishlisted($product_id)
    {
        $user_id = get_current_user_id();

        // Retrieve wishlist from cookies
        $wishlist = isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];

        // Check if the product_id is in the wishlist array
        return is_array($wishlist) && array_key_exists($product_id, $wishlist);
    }
    public function display_wishlist_icon() {
        // Get the wishlist URL and count
       // $wishlist_url = get_wishlist_url();
        $wishlist_count = WishlistButtonHandler::get_wishlist_count();
        ?>
        <div class="wishlist-icon-wrapper">
            <a href="/wqpn-my-wishlist" class="wishlist-icon">
                <span class="wqpn-wishlist-text">Wishlist</span>
                <span class="wqpn-wishlist-count" id="wqpn-wishlist-count"><?php echo $wishlist_count; ?></span>
            </a>
        </div>
        <?php
    }



}
