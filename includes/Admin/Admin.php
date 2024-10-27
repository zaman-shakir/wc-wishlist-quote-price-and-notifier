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
        add_action('admin_footer', [$this, 'enqueue_datatables_init']);
    }
    public function enqueue_datatables_init()
    {
        $screen = get_current_screen();

        // Check if the current screen is your plugin's admin page
        //if ($screen->id === 'wqpn-wishlist-quote-price') { // Replace with your actual screen ID
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    console.log("initialize datatable");
                    jQuery('#wishlist-quote-table').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "pageLength": 10,
                        "order": [[ 0, "asc" ]] // Order by first column ascending
                    });
                });
            </script>
            <?php
       // }
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
        // Main menu
        add_menu_page(
            'WQPN Wishlist & Quote Price', // Page title
            'WQPN Wishlist & Quote Price', // Menu title
            'manage_options', // Capability
            'wqpn-wishlist-quote-price', // Menu slug
            [$this, 'render_dashboard_menu_settings_page'], // Callback for dashboard
            'dashicons-heart', // Icon URL
            25 // Position
        );

        // Submenus
        add_submenu_page(
            'wqpn-wishlist-quote-price', // Parent slug
            'All Quotations/Dashboard',  // Page title
            'Dashboard',                // Menu title
            'manage_options',           // Capability
            'wqpn-dashboard',           // Menu slug
            [$this, 'render_dashboard_page']  // Callback function
        );

        add_submenu_page(
            'wqpn-wishlist-quote-price',
            'Settings',
            'Settings',
            'manage_options',
            'wqpn-settings',
            [$this, 'render_settings_page']  // Callback function for settings page
        );

        add_submenu_page(
            'wqpn-wishlist-quote-price',
            'Reports',
            'Reports',
            'manage_options',
            'wqpn-reports',
            [$this, 'render_reports_page']  // Callback function for reports page
        );

        add_submenu_page(
            'wqpn-wishlist-quote-price',
            'Messages',
            'Messages',
            'manage_options',
            'wqpn-messages',
            [$this, 'render_messages_page']  // Callback function for messages page
        );
    }

    public function render_dashboard_page()
    {
        echo '<h1>Dashboard</h1>';
        // Add your dashboard code here
    }

    public function render_settings_page()
    {
        echo '<h1>Settings</h1>';
        // Add your settings code here
    }

    public function render_reports_page()
    {
        echo '<h1>Reports</h1>';
        // Add your reports code here
    }

    public function render_messages_page()
    {
        echo '<h1>Messages</h1>';
        // Add your messages code here
    }

    public function render_dashboard_menu_settings_page()
    {
        ?>
        <div class="wrap">
            <h1>WQPN Wishlist & Quote Price</h1>

                <div class="wqpn-wishlist-quote-filter" style="margin-top:30px; margin-bottom:00px;">
                <form method="POST">
                <select name="wqpn-wishlist-quote-status" id="wqpn-wishlist-quote-status" class="regular-text">
                    <option value="">Select Status</option>
                    <option value="wqpn-all">All</option>
                    <option value="wqpn-waiting-response">Waiting for response</option>
                    <option value="wqpn-accepted">Accepted</option>
                    <option value="wqpn-waiting-used">Waiting to be used</option>
                    <option value="wqpn-offered-used">Offered price already used</option>
                    <option value="wqpn-declined">Declined</option>
                </select>
                <button type="submit" class="button button-primary">Filter</button>
                </form>
            </div>


        <br><br>
        </div>
            <table id="wishlist-quote-table" class="display compact" style="width:100%">
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
                //var_dump($_POST);
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
        // get filter criteria
        $filer_by = isset($_POST['wqpn-wishlist-quote-status']) ?? "wqpn-all";

        // $filer_by == wqpn-all == only order by archived;
        //$filer_by == wqpn-waiting-response ==  db status : submitted, archived : 0, used : 0;
        //$filer_by == wqpn-accepted ==  db status : accepted, archived : 0, used : 0;
        //$filer_by == wqpn-waiting-used ==  db status : accepted, archived : 0, used : 0;
        //$filer_by == wqpn-offered-used ==  db status : accepted, archived : 0, used : 0;
        //$filer_by == wqpn-declined ==  db status : accepted, archived : 0, used : 0;

        $filer_by = isset($_POST['wqpn-wishlist-quote-status']) ? $_POST['wqpn-wishlist-quote-status'] : 'wqpn-all';

        global $wpdb;
        $table_name = $wpdb->prefix . 'wqpn_wishlist';

        // Define the query based on the selected filter
        switch ($filer_by) {
            case 'wqpn-waiting-response':
                $query = $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE status = %s AND archived = %d AND used = %d ORDER BY archived",
                    'submitted',
                    0,
                    0
                );
                break;
            case 'wqpn-accepted':
                $query = $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE status = %s AND archived = %d AND used = %d ORDER BY archived",
                    'accepted',
                    0,
                    0
                );
                break;
            case 'wqpn-waiting-used':
                $query = $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE status = %s AND archived = %d AND used = %d ORDER BY archived",
                    'accepted',
                    0,
                    0
                );
                break;
            case 'wqpn-offered-used':
                $query = $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE status = %s AND archived = %d AND used = %d ORDER BY archived",
                    'accepted',
                    0,
                    0
                );
                break;
            case 'wqpn-declined':
                $query = $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE status = %s AND archived = %d AND used = %d ORDER BY archived",
                    'declined',
                    0,
                    0
                );
                break;
            default: // 'wqpn-all' or any other value
                $query = "SELECT * FROM $table_name ORDER BY archived";
                break;
        }

        $all_users_data = $wpdb->get_results($query, ARRAY_A);
        // global $wpdb;
        // $table_name = $wpdb->prefix . 'wqpn_wishlist';
        // $all_users_data = $wpdb->get_results(
        //     $wpdb->prepare(
        //         "SELECT * FROM $table_name Order by archived",
        //     ),
        //     ARRAY_A
        // );

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
