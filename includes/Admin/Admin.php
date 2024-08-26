<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Admin;

class Admin
{
    private static $instance = null;

    private function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('wp_ajax_accept_offer', [$this, 'accept_offer']);
        add_action('wp_ajax_reject_offer', [$this, 'reject_offer']);

    }

    // Returns the singleton instance of the class
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function reject_offer()
    {
        check_ajax_referer('wqpn_reject_offer', 'security');
        $unique_id = isset($_POST['unique_id']) ? sanitize_text_field($_POST['unique_id']) : '';
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';

        if (!$user_id) {
            wp_send_json_error('Invalid request');
        }

        // $all_users_data = get_transient('wqpn_wishlist');

        //  get user using user id, archived - 0
        // $all_users_data = get_transient('wqpn_wishlist');
        global $wpdb;
        $table_name = $wpdb->prefix . 'wqpn_wishlist';
        $user_data = $wpdb->get_row(
            $wpdb->prepare(
                // "SELECT * FROM $table_name WHERE archived = 0 AND user_id = %d LIMIT 1",
                "SELECT * FROM $table_name Where archived = 0 AND user_id = %d",
                $user_id
            ),
            ARRAY_A
        );

        if (isset($user_data)) {
            //$user_data =user_data;
            //$user_data['status'] = 'rejected';
            //$all_users_data[$user_id] = $user_data;
            //set_transient('wqpn_wishlist', $all_users_data);
            $wpdb->update(
                $table_name,
                [
                    'status' => 'rejected',
                    'archived'  => 1
                ], // Data to update
                ['id' => $user_data['id']], // Where clause
                ['%s'], // Data format
                ['%d']  // Where clause format
            );

            // Store the offered price in user meta
            $user_info = get_user_by('email', $user_data['email']);
            // if ($user_info) {
            //     update_user_meta($user_info->ID, 'wqpn_offered_price', $user_data['quote_price']);
            // }

            wp_send_json_success('Offer Rejected');
        } else {
            wp_send_json_error('Wishlist not found');
        }
    }
    public function accept_offer()
    {
        check_ajax_referer('wqpn_accept_offer', 'security');
        $unique_id = isset($_POST['unique_id']) ? sanitize_text_field($_POST['unique_id']) : '';
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';

        if (!$user_id) {
            wp_send_json_error('Invalid request');
        }

        $all_users_data = get_transient('wqpn_wishlist');

        //  get user using user id, archived - 0
        // $all_users_data = get_transient('wqpn_wishlist');
        global $wpdb;
        $table_name = $wpdb->prefix . 'wqpn_wishlist';
        $user_data = $wpdb->get_row(
            $wpdb->prepare(
                // "SELECT * FROM $table_name WHERE archived = 0 AND user_id = %d LIMIT 1",
                "SELECT * FROM $table_name Where archived = 0 AND user_id = %d",
                $user_id
            ),
            ARRAY_A
        );

        if (isset($user_data)) {
            //$user_data =user_data;
            $user_data['status'] = 'accepted';
            $all_users_data[$user_id] = $user_data;
            set_transient('wqpn_wishlist', $all_users_data);
            $wpdb->update(
                $table_name,
                ['status' => 'accepted'], // Data to update
                ['id' => $user_data['id']], // Where clause
                ['%s'], // Data format
                ['%d']  // Where clause format
            );

            // Store the offered price in user meta
            $user_info = get_user_by('email', $user_data['email']);
            if ($user_info) {
                update_user_meta($user_info->ID, 'wqpn_offered_price', $user_data['quote_price']);
            }

            wp_send_json_success('Offer accepted');
        } else {
            wp_send_json_error('Wishlist not found');
        }
    }
    public function add_menu_page()
    {
        add_menu_page(
            'WQPN Wishlist & Quote Price', // Page title
            'WQPN Wishlist & Quote Price', // Menu title
            'manage_options', // Capability
            'wqpn-wishlist-quote-price', // Menu slug
            [$this, 'render_settings_page'], // Callback
            'dashicons-heart', // Icon URL
            25 // Position
        );
    }

    public function render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1>WQPN Wishlist & Quote Price</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width:2%;">#</th>
                        <th>User</th>
                        <th>Product x Qty</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Fetch and display data
                $this->display_user_wishlists();
        ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private function display_user_wishlists()
    {
        // $all_users_data = get_transient('wqpn_wishlist');
        global $wpdb;
        $table_name = $wpdb->prefix . 'wqpn_wishlist';
        $all_users_data = $wpdb->get_results(
            $wpdb->prepare(
                // "SELECT * FROM $table_name WHERE archived = 0 AND user_id = %d LIMIT 1",
                "SELECT * FROM $table_name Order by archived",
            ),
            ARRAY_A
        );

        $row_number = 1;

        if (isset($all_users_data)) {
            foreach ($all_users_data as $user_data) {


                $user_info = get_user_by('email', $user_data['email']);
                $user_email = $user_info ? $user_info->user_email : 'Unknown';
                $user_name = $user_info ? $user_info->display_name : 'Unknown';
                $unique_num = $user_data['unique_id'];
                //$last_login = $user_info ? get_user_meta($user_info->ID, 'last_login', true) : 'Unknown';

                echo '<tr>';
                echo '<td >' . esc_html($row_number++) . '</td>';
                echo '<td> Name: ' . esc_html($user_name) .
                '<br>Email: ' . esc_html($user_email) .
                // '<br>' . esc_html($user_data['email']) .
                '<br> Whatsapp: ' . esc_html($user_data['whatsapp']) .
                '<br> Telegram: ' . esc_html($user_data['telegram']) .
                // '<br>Last Login: ' . esc_html($last_login) .
                '</td>';

                echo '<td>';
                $product_count = 1;
                $products = json_decode($user_data['products'], true);
                foreach ($products as $product_id => $product_data) {
                    //var_dump($product_data);


                    $product = wc_get_product($product_id);
                    $product_title = $product ? $product->get_title() : 'Unknown Product';
                    $product_image = $product ? wp_get_attachment_image_src($product->get_image_id(), [100, 100])[0] : '';
                    $product_stock_status = $product ? $product->is_in_stock() ? 'In Stock' : 'Out of Stock' : 'N/A';

                    echo '<div class="wqpn-single-product">';
                    echo '<span style="min-width:12px;" > ' . esc_html($product_count++) . '. </span> ';
                    echo '<details>';
                    echo '<summary style="cursor:pointer;">' . esc_html($product_title) . '<strong> x</strong> ' . esc_html($product_data['qty']) . '</summary>';
                    echo '<div  class="wqpn-single-product-details">';
                    if ($product_image) {
                        echo '<img src="' . esc_url($product_image) . '" alt="' . esc_attr($product_title) . '" width="100" height="100"><br>';
                    }
                    echo '<div><a target="_blank" href="' . esc_url(get_permalink($product_id)) . '">' . esc_html($product_title) . '</a><br>';
                    echo 'Price: ' . wp_kses_post($this->format_price($product->get_price())) . '<br>';
                    echo 'Stock: ' . esc_html($product_stock_status);
                    echo '</div></div>';
                    echo '</details>';
                    echo '</div>';
                }
                echo '</td>';

                echo '<td> Wishlist Total:  ' . wp_kses_post($this->format_price($user_data['wishlist_price'])) . '<br>Offered Price: ' . wp_kses_post($this->format_price($user_data['quote_price'])) . '</td>';
                echo '<td>' . esc_html($user_data['status']) . '</td>';
                if($user_data['status'] == 'submitted') {
                    echo '<td>
                    <a href="#" id="wqpn_button_accept_offer" class="button accept-offer wqpn_button_accept_offer" data-user-id="'.esc_attr($user_info->ID).'" data-unique-id="' . esc_attr($unique_num) . '" data-nonce="' . wp_create_nonce('wqpn_accept_offer') . '">Accept</a>
                    <a href="#" id="wqpn_button_reject_offer"  class="button reject-offer wqpn_button_reject_offer"  data-user-id="'.esc_attr($user_info->ID).'" data-unique-id="' . esc_attr($unique_num) . '" data-nonce="' . wp_create_nonce('wqpn_reject_offer') . '">Reject</a><span id="wqpn_accepted_offer" ">Accepted</span><span id="wqpn_rejected_offer" ">Rejected</span>
                    </td>';
                } elseif($user_data['status'] == 'accepted') {
                    echo '<td><h5>Accepted</h5>';
                    if($user_data['used']) {
                        echo '<br> Offered used already';
                    }
                    echo '</td>';
                } else {
                    echo '<td><h5>Declined</h5></td>';

                }
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No wishlist data found.</td></tr>';
        }
    }

    private function format_price($price)
    {
        return wc_price($price);
    }
}
