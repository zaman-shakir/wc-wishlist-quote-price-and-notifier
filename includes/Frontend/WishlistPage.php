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
    public function wqpn_display_wishlist()
    {
        // Retrieve wishlist from cookies
        $wishlist = isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];

        if (empty($wishlist)) {
            return '<p>Your wishlist is empty.</p>';
        }

        ob_start();

        // Wishlist Table
        echo '<div class="wqpn-wishlist-container">';
        echo '<div class="wqpn-wishlist-table-container">';
        echo '<table class="wqpn-wishlist-table">';
        echo '<tbody>';

        foreach ($wishlist as $item) {

            $product_id = $item['product_id'];
            $added_time = $item['added_time'];

            $product = wc_get_product($product_id);
            if (!$product) {
                continue;
            }

            $product_title = $product->get_title();
            $product_price = $product->get_price_html();
            $product_image = $product->get_image();
            $product_qty = 1; // Default quantity for wishlist, adjust as needed

            $nonce = wp_create_nonce('_wishlist_quote_price_notify');
            $url = esc_url(admin_url('admin-ajax.php'));
            $svg = '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"></path></svg>';
            $remove_btn = "<div class= 'wqpn-wishlist-remove wqpn-wishlish-page-remove' data-product-id='{$product_id}' data-nonce='{$nonce}' data-url='{$url}'>".$svg."</div>";
            $update_qty_btn = "<button class='btn wqpn-wishlist-update-qty' data-product-id='{$product_id}' data-nonce='{$nonce}' data-url='{$url}'>Update</button>";
            // <td class='wqpn-product-details'><a target href='{$product->get_permalink()}'>{$product_title}</a><br>{$product_price}<br>July 16, 2024<br><span class='wqpn-product-stock'>In stock</span></td>
            //F j, Y
            echo "<tr id='wqpn-row-{$product_id}'>
                <td class='wqpn-img'>{$product_image}</td>
                <td class='wqpn-product-details'><strong><a target='_blank' href='{$product->get_permalink()}'>{$product_title}</a></strong><br>{$product_price}<br>July 16, 2024<br><span class='wqpn-product-stock'>In stock</span></td>
                <td class='wqpn-product-qty'>
                    <input type='number' name='qty[{$product_id}]' value='{$product_qty}' min='1' class='wqpn-qty-input'/>
                </td>
                <td class='wqpn-product-remove'>{$remove_btn}</td>
            </tr>";
        }

        echo '</tbody></table>';
        echo '</div>';

        // Order Summary
        echo '<div class="wqpn-order-summary">
            <h3>Order summary</h3>
            <p>Subtotal: <span id="wqpn-subtotal"></span></p>
            <p>Order total: <span id="wqpn-total"></span></p>
            <button class="btn wqpn-submit-price">Submit Price</button>
        </div>';
        echo '</div>';

        return ob_get_clean();
    }



    // Function to display wishlist
    public function wqpn_display_wishlist2()
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
            //$is_wishlisted = $this->is_wishlisted($product_id);
            //$icon = $is_wishlisted ? 'wqpn-wishlist-full' : 'wqpn-wishlist-empty';
            $nonce = wp_create_nonce('_wishlist_quote_price_notify');
            $url = esc_url(admin_url('admin-ajax.php'));
            //$label = $is_wishlisted ? __('Remove from Wishlist', 'wishlist') : __('Add to Wishlist', 'wishlist');
            $product = wc_get_product($product_id);
            $added_time = date('Y-m-d H:i:s', $item['added_time']);
            $remove_btn = "<button class='btn wqpn-wishlish-page-remove' data-product-id='".$product_id."' data-nonce='".$nonce."' data-url='".$url."'>Remove</button>";
            echo '<tr id="row-'.$product_id.'">';
            echo '<td>' . $product->get_title() . '</td>';
            echo '<td>' . $product->get_price_html() . '</td>';
            echo '<td>' . $added_time . '</td>';
            echo '<td>' . $remove_btn . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        return ob_get_clean();
    }
    public function wqpn_display_wishlist5()
    {
        // Retrieve wishlist from cookies
        $wishlist = isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];

        if (empty($wishlist)) {
            return '<p>Your wishlist is empty.</p>';
        }

        ob_start();

        echo '<table class="wqpn-wishlist-table">';
        echo '<thead><tr>
            <th></th>
            <th></th>
            <th>Product</th>
            <th>Unit Price</th>
            <th>Date Added</th>
            <th>Stock Status</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr></thead>';
        echo '<tbody>';

        foreach ($wishlist as $item) {
            $product_id = $item;
            $added_time = $item['added_time'];

            $product = wc_get_product($product_id);
            if (!$product) {
                continue;
            }

            $product_title = $product->get_title();
            $product_price = $product->get_price_html();
            $product_image = $product->get_image();
            $product_stock_status = $product->is_in_stock() ? '<span class="in-stock">In stock</span>' : '<span class="out-of-stock">Out of stock</span>';
            $product_qty = 1; // Default quantity for wishlist, adjust as needed
            $product_subtotal = wc_price($product->get_price() * $product_qty);

            $nonce = wp_create_nonce('_wishlist_quote_price_notify');
            $url = esc_url(admin_url('admin-ajax.php'));

            $remove_btn = "<button class='btn wqpn-wishlish-page-remove' data-product-id='{$product_id}' data-nonce='{$nonce}' data-url='{$url}'>Remove</button>";

            echo "<tr id='row-{$product_id}'>
                <td><input type='checkbox' name='wishlist_select[]' value='{$product_id}' /></td>
                <td><span class='drag-handle'>&#9776;</span></td>
                <td><span class='wqpn-product-image' style='width:50px;'> {$product_image}</span> <a href='{$product->get_permalink()}'>{$product_title}</a></td>
                <td>{$product_price}</td>
                <td>" . date('F j, Y', strtotime($added_time)) . "</td>
                <td>{$product_stock_status}</td>
                <td><input type='number' name='qty[{$product_id}]' value='{$product_qty}' min='1' /></td>
                <td>{$product_subtotal}</td>
                <td><a href='{$url}' class='btn add-to-cart-button'>Add to Cart</a></td>
            </tr>";
        }

        echo '</tbody></table>';

        return ob_get_clean();
    }


}
