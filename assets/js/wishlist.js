(function ($) {
    "use strict";

    // Generalized AJAX Function
    window.wqpn_ajax_action = function (url, callback, method, data, sendJSON = true) {
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                try {
                    var responseData = JSON.parse(xmlhttp.responseText);
                } catch (err) {
                    console.warn(err.message + " in " + xmlhttp.responseText, err);
                    return;
                }
                callback(responseData);
            }
        };
        xmlhttp.open(method, url, true);
        if (sendJSON) {
            xmlhttp.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            data = JSON.stringify(data);
        } else {
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
        }
        xmlhttp.send(data);
    };

    // Callback function for AJAX response
    function wishlist_callback(responseData) {
        if (responseData.status === 'ok') {
            var button = document.querySelector(`[data-product-id="${responseData.product_id}"]`);
            if (responseData.action === 'add') {
                button.classList.remove('wqpn-wishlist-empty');
                button.classList.add('wqpn-wishlist-full');
                button.textContent = 'Remove from Wishlist';
            } else {
                button.classList.remove('wqpn-wishlist-full');
                button.classList.add('wqpn-wishlist-empty');
                button.textContent = 'Add to Wishlist';
            }
        } else {
            console.error('Failed to perform wishlist action:', responseData.message);
        }
    }

    // Wishlist Button Click Handler
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.wqpn-wishlist-button').forEach(function (button) {
            button.addEventListener('click', function () {
                var productId = this.dataset.productId;
                var nonce = this.dataset.nonce;
                var action = this.classList.contains('wqpn-wishlist-full') ? 'remove_from_wishlist' : 'add_to_wishlist'; // Update actions accordingly
                //var url = wqpn_ajax.ajax_url; // Use the correct AJAX URL
                var url = this.dataset.url; // Use the correct AJAX URL
                var data = {
                    action: action,
                    product_id: productId,
                    _wpnonce: nonce
                };

                window.wqpn_ajax_action(url, wishlist_callback, 'POST', data);
            });
        });
    });

})(jQuery);
