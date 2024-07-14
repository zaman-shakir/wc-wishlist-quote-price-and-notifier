<?php

namespace Shakir\WishlistQuotePriceAndNotifier\Frontend;

class WishlistButton
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('woocommerce_after_shop_loop_item', [$this, 'display_wishlist_button'], 20);
        add_action('woocommerce_before_add_to_cart_form', [$this, 'display_wishlist_button'], 35);
        //add_action('wp_ajax_toggle_wishlist', [$this, 'toggle_wishlist']);
        //add_action('wp_ajax_nopriv_toggle_wishlist', [$this, 'toggle_wishlist']);
    }

    public function enqueue_assets()
    {
        //wp_enqueue_style('dashicons');
        //wp_enqueue_script('wishlist-button', plugins_url('/assets/js/wishlist-button.js', __FILE__), ['jquery'], null, true);
        // wp_localize_script('wishlist-button', 'wishlistButton', [
        //    'ajax_url' => admin_url('admin-ajax.php')
        //]);
    }
    public function display_wishlist_button2()
    {
        global $product;

        $product_id = $product->get_id();
        $is_wishlisted = $this->is_wishlisted($product_id);
        $icon = $is_wishlisted ? 'wqpn-wishlist-full' : 'wqpn-wishlist-empty';
        $nonce =  wp_create_nonce('_wishlist_quote_price_notify');
        $action = 'wc_ajax_click_wishlist_button';
        $url = esc_url(admin_url('admin-ajax.php'));
        $label = $is_wishlisted ? __('Remove from Wishlist', 'wishlist') : __('Add to Wishlist', 'wishlist');

        echo sprintf(
            '<div class="wqpn-wishlit"><span class="wqpn-wishlist-button %s" data-product-id="%d" data-nonce="%s" data-action="%s" data-url="%s"> %s</span></div>',
            esc_attr($icon),
            esc_attr($product_id),
            esc_attr($nonce),
            esc_attr($action),
            esc_attr($url),
            esc_html($label),
        );
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
        $wishlist = get_user_meta($user_id, 'wishlist', true);

        return is_array($wishlist) && in_array($product_id, $wishlist);
    }
    public function toggle_wishlist()
    {
        if (!is_user_logged_in() || !isset($_POST['product_id'])) {
            wp_send_json_error();
        }

        $product_id = intval($_POST['product_id']);
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, 'wishlist', true);

        if (!is_array($wishlist)) {
            $wishlist = [];
        }

        if (in_array($product_id, $wishlist)) {
            $wishlist = array_diff($wishlist, [$product_id]);
            $label = __('Add to Wishlist', 'wishlist');
        } else {
            $wishlist[] = $product_id;
            $label = __('Remove from Wishlist', 'wishlist');
        }

        update_user_meta($user_id, 'wishlist', $wishlist);

        wp_send_json_success(['label' => $label]);
    }

}
