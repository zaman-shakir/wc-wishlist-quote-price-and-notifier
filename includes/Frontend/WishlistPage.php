<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

class WishlistPage
{
    public function __construct()
    {
        $this->wqpn_wishlist_page_contents_shortcode();
    }

    public function wqpn_wishlist_page_contents_shortcode()
    {
        // Register shortcode to display wishlist
        add_shortcode('wqpn_wishlist', [$this, 'wqpn_display_wishlist']);
    }

    // Function to display wishlist
    public function wqpn_display_wishlist()
    {

        // Retrieve wishlist from cookies
        $wishlist = isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];
        var_dump($wishlist);
        if (empty($wishlist)) {
            return '<p>Your wishlist is empty.</p>';
        }

        ob_start();

        echo '<table class="wqpn-wishlist-table">';
        echo '<thead><tr><th>Product</th><th>Price</th><th>Added Time</th><th>Actions</th></tr></thead>';
        echo '<tbody>';

        foreach ($wishlist as $item) {

            $product_id = $item;
            $product = wc_get_product($product_id);
            $added_time = date('Y-m-d H:i:s', $item['added_time']);

            echo '<tr>';
            echo '<td>' . $product->get_title() . '</td>';
            echo '<td>' . $product->get_price_html() . '</td>';
            echo '<td>' . $added_time . '</td>';
            echo '<td><button class="wqpn-remove-button" data-product-id="' . esc_attr($product_id) . '">Remove</button></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        return ob_get_clean();
    }

}
