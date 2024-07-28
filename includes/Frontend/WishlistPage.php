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

        //get data from transient to see if already offered price data exists
        // & status is review accepted or rejected
        $user_id = get_current_user_id();
        $all_users_data = get_transient('wqpn_wishlist');
        if (isset($all_users_data[$user_id])) {
            $user_data = $all_users_data[$user_id];
            $status = $user_data['status'];
            $wishlist_price = $user_data['wishlist_price'];
            var_dump($all_users_data);
            // (array) [1 element]
            // 1:
            // (array) [10 elements]
            // wishlist_price: (string) "45"
            // quote_price: (string) "45"
            // products:
            // (array) [1 element]
            // 59:
            // (array) [4 elements]
            // product_id: (integer) 59
            // added_time: (integer) 1722195473
            // qty: (integer) 1
            // subtotal: (integer) 45
            // submitted_time: (string) "2024-07-29 01:44:24"
            // status: (string) "submitted"
            // ip_address: (string) "::1"
            // unique_id: (string) "6c7c96b1-6abd-45ff-9d13-20647301d942"
            // email: (string) "zaman.shakirdev@gmail.com"
            // whatsapp: (string) "+1 (473) 451-3366"
            // telegram: (string) "+1 (749) 915-8748"
            // Use the status and wishlist price as needed
        }

        $wishlist = $this->get_wishlist_from_cookies();

        $user_applied = isset($_COOKIE['wqpn_user_applied_for_submit_form']) ? true : false;

        if (empty($wishlist)) {
            return '<p>Your wishlist is empty.</p>';
        }

        $currency_symbol = get_woocommerce_currency_symbol();
        $currency_position = get_option('woocommerce_currency_pos');

        ob_start();
        $this->display_wishlist_table($wishlist, $currency_symbol, $currency_position);
        $this->display_order_summary($currency_symbol, $currency_position);
        //$this->display_price_section($currency_symbol, $currency_position);
        return ob_get_clean();
    }

    private function get_wishlist_from_cookies()
    {
        return isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];
    }

    private function display_wishlist_table($wishlist, $currency_symbol, $currency_position)
    {
        $action_url = admin_url('admin-post.php');
        $user_applied = isset($_COOKIE['wqpn_user_applied_for_submit_form']) ? true : false;

        if($user_applied) {
            echo '<form id="wqpn-wishlist-form-submit" method="post" action="' . esc_url($action_url) . '"><div class="wqpn-wishlist-container">';
            echo '<input type="hidden" name="action" value="wqpn_submit_quote_price">';
            echo '<div class="wqpn-wishlist-submit-table-container" style="width:50%">';
            echo '<table class="wqpn-wishlist-table"><tbody>';
            echo "<tr id='wqpn-row'>
            <td class='wqpn-img'><strong>Product</strong></td>
            <td class='wqpn-product-details'>
            &nbsp;
            </td>
            <td class='wqpn-product-qty'>  <strong>Qty</strong>

            </td>
            <td class='wqpn-product-subtotal' id='wqpn-subtotal'><strong>Subtotal</strong></td>
        </tr>";
        } else {
            echo '<form id="wqpn-wishlist-form" method="post" action="' . esc_url($action_url) . '"><div class="wqpn-wishlist-container">';
            echo '<input type="hidden" name="action" value="wqpn_submit_price">';
            echo '<div class="wqpn-wishlist-table-container">';
            echo '<table class="wqpn-wishlist-table"><tbody>';
        }
        foreach ($wishlist as $item) {
            $this->display_wishlist_item($item, $currency_symbol, $currency_position);
        }

        echo '</tbody></table>';
        echo '</form></div>';
    }

    private function display_wishlist_item($item, $currency_symbol, $currency_position)
    {
        $user_applied = isset($_COOKIE['wqpn_user_applied_for_submit_form']) ? true : false;

        $product_id = $item['product_id'];
        $added_time = $item['added_time'];

        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $product_title = $product->get_title();
        $product_price_html = $product->get_price_html();
        $product_price = $product->get_price();
        $product_image = $product->get_image();
        $product_qty = 1; // Default quantity for wishlist, adjust as needed
        $added_time_formatted = date('F j, Y', strtotime($added_time));

        $stock_status = $product->is_in_stock() ? 'In stock' : 'Out of stock';
        $stock_class = $product->is_in_stock() ? 'wqpn-in-stock' : 'wqpn-out-stock';

        $subtotal = $product_price * $product_qty;

        $nonce = wp_create_nonce('_wishlist_quote_price_notify');
        $url = esc_url(admin_url('admin-ajax.php'));
        $remove_btn = $this->get_remove_button($product_id, $nonce, $url);

        $formatted_subtotal = $this->format_price($subtotal, $currency_symbol, $currency_position);
        if($user_applied) {
            echo "<tr id='wqpn-row-{$product_id}'>
            <td class='wqpn-img'>{$product_image}</td>
            <td class='wqpn-product-details'>
                <strong><a target='_blank' href='{$product->get_permalink()}'>{$product_title}</a></strong><br>
                {$product_price_html}<br>
                {$added_time_formatted}<br>
                <span class='wqpn-product-stock {$stock_class}'>{$stock_status}</span>
            </td>
            <td class='wqpn-product-qty'>{$product_qty}
            </td>
            <td class='wqpn-product-subtotal' id='wqpn-subtotal-{$product_id}' data-product-id='{$product_id}' data-product-price='{$product_price}'>". $formatted_subtotal ."</td>
        </tr>";
        } else {
            echo "<tr id='wqpn-row-{$product_id}'>
            <td class='wqpn-img'>{$product_image}</td>
            <td class='wqpn-product-details'>
                <strong><a target='_blank' href='{$product->get_permalink()}'>{$product_title}</a></strong><br>
                {$product_price_html}<br>
                {$added_time_formatted}<br>
                <span class='wqpn-product-stock {$stock_class}'>{$stock_status}</span>
            </td>
            <td class='wqpn-product-qty'>
                <input type='number' class='wqpn-product-qty-input' name='qty[{$product_id}]' value='{$product_qty}' min='1' data-product-id='{$product_id}' data-product-price='{$product_price}'/>
            </td>
            <td class='wqpn-product-subtotal' id='wqpn-subtotal-{$product_id}' data-product-id='{$product_id}' data-product-price='{$product_price}'>". $formatted_subtotal ."</td>
            <td class='wqpn-product-remove'>{$remove_btn}</td>
        </tr>";
        }

    }

    private function get_remove_button($product_id, $nonce, $url)
    {
        $svg = '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"></path>
        </svg>';
        return "<div class='wqpn-wishlist-remove wqpn-wishlish-page-remove' data-product-id='{$product_id}' data-nonce='{$nonce}' data-url='{$url}'>" . $svg . "</div>";
    }

    private function display_order_summary($currency_symbol, $currency_position)
    {
        $user_applied = isset($_COOKIE['wqpn_user_applied_for_submit_form']) ? true : false;
        $wishlist_total = isset($_COOKIE['wqpn_total_price']) ? floatval($_COOKIE['wqpn_total_price']) : 0;

        if ($user_applied) {
            echo '<div class="wqpn-order-summary" style="width:47%">
                <h4 style="text-align:center">Offer Your Price</h4>
                <div class="wqpn-price-submit-grid">
                    <div>Wishlist Total:</div>
                    <div id="wqpn-wishlist-total">' . $this->format_price_for_submit($wishlist_total, $currency_symbol, $currency_position) . '</div>
                    <div>Your Price:</div>
                    <div><input name="quoteprice" type="number" value ="'.$wishlist_total.'" id="wqpn-price-input" step="0.01" /></div>
                    <div>Email:</div>
                    <div><input name="email" required type="email" id="wqpn-email-input" /></div>
                    <div>WhatsApp:</div>
                    <div><input name="whatsapp" type="tel" id="wqpn-whatsapp-input" /></div>
                    <div>Telegram:</div>
                    <div><input name="telegram" type="tel" id="wqpn-telegram-input" /></div>
                    <div style="grid-column: span 2; text-align: center;">
                        <button type="submit" id="">Submit Price</button>
                    </div>
                </div>
              </div>';
        } else {
            echo '<div class="wqpn-order-summary">
            <h3>Wishlist Summary</h3>
            <p>Wishlist Total: <span id="wqpn-total" data-currency-position="' . esc_attr($currency_position) . '" data-currency-symbol="' . esc_attr($currency_symbol) . '"></span></p>
            <button id="wqpn-submit-price" class="wqpn-submit-price">Submit a Price for these wishlist products?</button>
        </div>';
        }

    }
    private function format_price_for_submit($price, $currency_symbol, $currency_position)
    {
        if ($currency_position === 'before') {
            return $currency_symbol . number_format($price, 2);
        } else {
            return number_format($price, 2) . $currency_symbol;
        }
    }
    private function format_price($price, $currency_symbol, $currency_position)
    {
        switch ($currency_position) {
            case 'left':
                return $currency_symbol . number_format($price, 2);
            case 'right':
                return number_format($price, 2) . $currency_symbol;
            case 'left_space':
                return $currency_symbol . ' ' . number_format($price, 2);
            case 'right_space':
                return number_format($price, 2) . ' ' . $currency_symbol;
            default:
                return $currency_symbol . number_format($price, 2);
        }
    }

    ////////////////////////////////////////////////////////////////
    private function display_price_section($currency_symbol, $currency_position)
    {
        echo '<div id="wqpn-price-section" style="display: none;">
            <div class="wqpn-price-submit-grid">
                <div class="wqpn-price-submit-title" style="grid-column: span 2; text-align: center;">Quote a Price</div>
                <div>Wishlist Total:</div>
                <div id="wqpn-wishlist-total">' . $this->format_price(0, $currency_symbol, $currency_position) . '</div>
                <div>Your Price:</div>
                <div><input type="number" id="wqpn-price-input" step="0.01" /></div>
                <div>Email:</div>
                <div><input type="email" id="wqpn-email-input" /></div>
                <div>WhatsApp:</div>
                <div><input type="text" id="wqpn-whatsapp-input" /></div>
                <div>Telegram:</div>
                <div><input type="text" id="wqpn-telegram-input" /></div>
                <div style="grid-column: span 2; text-align: center;">
                    <button id="wqpn-price-submit">Submit Price</button>
                </div>
            </div>
        </div>';
    }



    public static function wqpn_is_user_logged_in()
    {

        $response = false;
        if (is_user_logged_in()) {
            $response = true;
            echo json_encode($response);
        } else {
            echo json_encode($response);
        }
        wp_die(); // This is required to terminate immediately and return a proper response
    }
    public static function handle_wishlist_form_submission()
    {
        // Ensure the request is valid
        if (!isset($_POST['action']) || $_POST['action'] !== 'wqpn_submit_price') {
            wp_die('Invalid form submission');
        }

        // Retrieve the current wishlist from the cookie
        $wishlist = isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];

        // Update the wishlist with the submitted quantities
        if (isset($_POST['qty']) && is_array($_POST['qty'])) {
            $total_price = 0;

            foreach ($_POST['qty'] as $product_id => $qty) {
                $product_id = intval($product_id);
                $qty = intval($qty);

                // Assume a function `get_product_price($product_id)` that returns the product price
                $product_price = self::get_product_price($product_id);
                $subtotal = $product_price * $qty;

                // Update the wishlist item
                if (isset($wishlist[$product_id])) {
                    $wishlist[$product_id]['qty'] = $qty;
                    $wishlist[$product_id]['subtotal'] = $subtotal;
                }

                // Calculate the total price
                $total_price += $subtotal;
            }

            // Update the wishlist in the cookie
            setcookie('wqpn_wishlist', json_encode($wishlist), time() + 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
            setcookie('wqpn_total_price', $total_price, time() + 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
            setcookie('wqpn_user_applied_for_submit_form', 'true', time() + 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);

            // Redirect after submission
            wp_redirect(home_url('/my-wishlist?wishlist_updated=true'));
            exit;
        } else {
            wp_die('No quantities submitted');
        }
    }

    private static function get_product_price($product_id)
    {
        // Replace this with the actual logic to get the product price
        // For example, using WooCommerce:
        $product = wc_get_product($product_id);
        return $product ? $product->get_price() : 0;
    }
    public static function handle_wqpn_submit_quote_price()
    {
        // Collect POST data
        $user_id = get_current_user_id();
        $quote_price = $_POST['quoteprice'];
        $email = $_POST['email'];
        $whatsapp = $_POST['whatsapp'];
        $telegram = $_POST['telegram'];

        // Retrieve current wishlist from the cookie
        $wishlist = isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];
        $wishlist_total = isset($_COOKIE['wqpn_total_price']) ? $_COOKIE['wqpn_total_price'] : 0;

        // Prepare the data to be saved in the transient
        $transient_data = [
            'wishlist_price' => $wishlist_total,
            'quote_price' => $quote_price,
            'products' => $wishlist,
            'submitted_time' => current_time('mysql'),
            'status' => 'submitted',
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'unique_id' => wp_generate_uuid4(),
            'email' => $email,
            'whatsapp' => $whatsapp,
            'telegram' => $telegram
        ];

        // Retrieve existing transient data or initialize a new array
        $all_users_data = get_transient('wqpn_wishlist');
        if ($all_users_data === false) {
            $all_users_data = [];
        }

        // Update the transient with the current user's data
        $all_users_data[$user_id] = $transient_data;
        set_transient('wqpn_wishlist', $all_users_data);

        // Set cookies for 30 days
        setcookie('wqpn_user_applied_for_submit_form', '', time() - 3600, '/'); // Expire the old cookie
        setcookie('wqpn_wishlist', '', time() - 3600, '/'); // Expire the old cookie
        setcookie('wqpn_total_price', '', time() - 3600, '/'); // Expire the old cookie
        setcookie('wqpn_user_offered_custom_price', 'true', time() + (30 * DAY_IN_SECONDS), '/');

        // Redirect back to the wishlist page or any desired page
        wp_redirect(home_url('/my-wishlist')); // Replace '/wishlist-page-url' with the actual URL
        exit;
    }
}
