(function ($) {
    "use strict";

    // Callback function for AJAX response
    function wishlist_callback(responseData) {
        if (responseData.status === 201) {
            var button = document.querySelector(`[data-product-id="${responseData.product_id}"]`);
            if (button) {
                button.classList.remove(responseData.remove_class);
                button.classList.add(responseData.add_class);
                button.textContent = responseData.wishlist_action === 'add_to_wishlist' ? 'Remove from Wishlist' : 'Add to Wishlist';
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
                var wishlistAction = this.classList.contains('wqpn-wishlist-full') ? 'remove_from_wishlist' : 'add_to_wishlist';
                var action = 'click_wishlist_button';
                var url = this.dataset.url;
                var data = {
                    action: action,
                    product_id: productId,
                    _wpnonce: nonce,
                    wishlist_action: wishlistAction
                };
                console.log("Request Data:", data);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success: function (response) {
                        try {
                            var responseData = JSON.parse(response);
                            wishlist_callback(responseData);
                        } catch (err) {
                            console.warn('Error parsing JSON response:', err);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX request failed:', status, error);
                    }
                });
            });
        });
    });
    // Remove button handler for wishlist page
    document.querySelectorAll('.wqpn-remove-button').forEach(function (button) {
        button.addEventListener('click', function () {
            var productId = this.dataset.productId;
            var nonce = this.dataset.nonce; // You need to pass the nonce to the button data attributes
            var action = 'click_wishlist_button';
            var wishlistAction = 'remove_from_wishlist';
            var url = this.dataset.url; // Ensure the URL is passed to the button data attributes
            var data = {
                action: action,
                product_id: productId,
                _wpnonce: nonce,
                wishlist_action: wishlistAction
            };
            console.log("Request Data:", data);

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    try {
                        var responseData = JSON.parse(response);
                        console.log("Response Data:", responseData);
                        wishlist_callback(responseData);
                    } catch (err) {
                        console.warn('Error parsing JSON response:', err);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX request failed:', status, error);
                }
            });
        });
    });

})(jQuery);


/**
(function ($) {
    "use strict";

    // Generalized AJAX Function
    window.wqpn_ajax_action = function (url, callback, method, data, sendJSON = true) {
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4) {
                if (xmlhttp.status === 200) {
                    try {
                        var responseData = JSON.parse(xmlhttp.responseText);
                        callback(responseData);
                    } catch (err) {
                        console.warn(err.message + " in " + xmlhttp.responseText, err);
                    }
                } else {
                    console.error('Request failed with status:', xmlhttp.status);
                }
            }
        };

        xmlhttp.open(method, url, true);

        if (sendJSON) {
            xmlhttp.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            data = JSON.stringify(data); // Convert data to JSON string
        } else {
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
            data = Object.keys(data).map(key => key + '=' + encodeURIComponent(data[key])).join('&');
        }

        console.log("Sending Data:", data); // Log the data being sent
        xmlhttp.send(data);
    };

    // Callback function for AJAX response
    function wishlist_callback(responseData) {
        if (responseData.status === 'ok') {
            var button = document.querySelector(`[data-product-id="${responseData.product_id}"]`);
            if (responseData.wishlist_action === 'add_to_wishlist') {
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
                var wishlistAction = this.classList.contains('wqpn-wishlist-full') ? 'remove_from_wishlist' : 'add_to_wishlist';
                var action = 'click_wishlist_button';
                var url = this.dataset.url;
                var data = {
                    action: action,
                    product_id: productId,
                    _wpnonce: nonce,
                    wishlist_action: wishlistAction
                };
                console.log("Request Data:", data);
                window.wqpn_ajax_action(url, wishlist_callback, 'POST', data);
            });
        });
    });

})(jQuery);
*/
