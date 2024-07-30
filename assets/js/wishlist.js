(function ($) {
    "use strict";
    $(".wqpn_button_accept_offer").on("click", function (e) {
        e.preventDefault();

        var button = $(this);
        var uniqueId = button.data("unique-id");
        const userId = button.data("user-id");
        var nonce = button.data("nonce");

        $.ajax({
            url: ajaxurl, // WordPress AJAX handler URL
            method: "POST",
            data: {
                action: "accept_offer",
                unique_id: uniqueId,
                user_id: userId,
                security: nonce,
            },
            success: function (response) {
                if (response.success) {
                    button.closest("tr").find(".status").text("accepted");
                    alert("Offer accepted");
                    document.getElementById(
                        "wqpn_button_reject_offer"
                    ).innerHTML = "";
                    document.getElementById(
                        "wqpn_button_reject_offer"
                    ).style.display = "none";
                    document.getElementById(
                        "wqpn_button_accept_offer"
                    ).style.display = "none";
                    document.getElementById(
                        "wqpn_button_accept_offer"
                    ).innerHTML = "";
                    document.getElementById(
                        "wqpn_accepted_offer"
                    ).style.display = "block";
                    //wqpn_accepted_offer
                    //wqpn_button_reject_offer
                } else {
                    alert("Error: " + response.data);
                }
            },
        });
    });
    // Callback function for AJAX response
    function wishlist_callback(responseData) {
        if (responseData.status === 201) {
            var button = document.querySelector(
                `[data-product-id="${responseData.product_id}"]`
            );
            if (button) {
                button.classList.remove(responseData.remove_class);
                button.classList.add(responseData.add_class);
                button.textContent =
                    responseData.wishlist_action === "add_to_wishlist"
                        ? "Remove from Wishlist"
                        : "Add to Wishlist";
            }
        } else {
            console.error(
                "Failed to perform wishlist action:",
                responseData.message
            );
        }
    }

    // Function to update the wishlist total
    function updateWishlistTotal() {
        let total = 0;
        const subtotals = document.querySelectorAll(".wqpn-product-subtotal");
        if (
            subtotals.length > 0 &&
            document.getElementById("wqpn-submit-price")
        ) {
            subtotals.forEach((subtotal) => {
                const price = parseFloat(subtotal.dataset.productPrice);
                const qty = parseInt(
                    document.querySelector(
                        `input[data-product-id="${subtotal.dataset.productId}"]`
                    ).value,
                    10
                );
                total += price * qty;
            });

            const totalElement = document.getElementById("wqpn-total");
            const currencySymbol = totalElement.dataset.currencySymbol;
            const currencyPosition = totalElement.dataset.currencyPosition;
            let formattedTotal;

            switch (currencyPosition) {
                case "left":
                    formattedTotal = `${currencySymbol}${total.toFixed(2)}`;
                    break;
                case "right":
                    formattedTotal = `${total.toFixed(2)}${currencySymbol}`;
                    break;
                case "left_space":
                    formattedTotal = `${currencySymbol} ${total.toFixed(2)}`;
                    break;
                case "right_space":
                    formattedTotal = `${total.toFixed(2)} ${currencySymbol}`;
                    break;
                default:
                    formattedTotal = `${currencySymbol}${total.toFixed(2)}`;
            }

            totalElement.textContent = formattedTotal;
        }
    }

    function wishlist_page_callback(responseData) {
        if (responseData.status === 201) {
            let rowid = "wqpn-row-" + responseData.product_id;
            document.getElementById(rowid).remove();
            updateWishlistTotal(); // Update the total after removing the product
        } else {
            console.error(
                "Failed to perform wishlist action:",
                responseData.message
            );
        }
    }

    async function isUserLoggedIn() {
        var action = "is_user_logged_in";
        var url = "/wp-admin/admin-ajax.php";
        var data = {
            action: action,
        };
        console.log("Request Data:", data);

        try {
            let response = await $.ajax({
                url: url,
                method: "POST",
                data: data,
            });
            console.log("Response:", response);
            return response;
        } catch (error) {
            console.error("AJAX request failed:", error);
            throw error;
        }
    }

    // Wishlist Button Click Handler
    document.querySelectorAll(".wqpn-submit-price").forEach(function (button) {
        button.addEventListener("click", async function () {
            try {
                console.log("wqpn-submit-price clicked");
                let loggedIn = await isUserLoggedIn();
                console.log("Logged in status:", loggedIn);
                if (loggedIn) {
                    console.log("User is logged in: display the section");
                    //submit the form
                    let form = document.getElementById("wqpn-wishlist-form");

                    // Example: Ensure form is valid
                    if (form.checkValidity()) {
                        form.submit(); // Submit the form if valid
                    } else {
                        // Handle invalid form case
                        alert("Please fill out the required fields.");
                    }
                } else {
                    alert("Please log in to submit a price.");
                    // Optionally, you can redirect the user to the login page
                    // window.location.href = "/wp-login.php";
                }
            } catch (error) {
                console.error("Error checking login status:", error);
            }
        });
    });
    // Handle form submission
    // document
    //     .getElementById("wqpn-price-submit")
    //     .addEventListener("click", function () {
    //         var price = document.getElementById("wqpn-price-input").value;
    //         var email = document.getElementById("wqpn-email-input").value;
    //         var whatsapp = document.getElementById("wqpn-whatsapp-input").value;
    //         var telegram = document.getElementById("wqpn-telegram-input").value;

    //         // Validate input fields
    //         if (!price || !email || !whatsapp || !telegram) {
    //             alert("Please fill in all fields.");
    //             return;
    //         }

    //         // Prepare data to submit
    //         var data = {
    //             action: "submit_wishlist_price",
    //             price: price,
    //             email: email,
    //             whatsapp: whatsapp,
    //             telegram: telegram,
    //         };

    //         $.ajax({
    //             url: "/wp-admin/admin-ajax.php",
    //             method: "POST",
    //             data: data,
    //             success: function (response) {
    //                 alert("Price submitted successfully!");
    //                 location.reload();
    //             },
    //             error: function (xhr, status, error) {
    //                 console.error("AJAX request failed:", status, error);
    //             },
    //         });
    //     });
    // Quantity input change handler
    const qtyInputs = document.querySelectorAll(".wqpn-product-qty-input");
    qtyInputs.forEach((input) => {
        input.addEventListener("input", function () {
            const productId = this.dataset.productId;
            const price = parseFloat(this.dataset.productPrice);
            const qty = parseInt(this.value, 10);
            const subtotalElement = document.getElementById(
                `wqpn-subtotal-${productId}`
            );
            const subtotal = price * qty;
            subtotalElement.innerHTML = `$${subtotal.toFixed(2)}`;
            updateWishlistTotal();
        });
    });

    // Initial call to update the total
    updateWishlistTotal();
    // Wishlist Button Click Handler
    document.addEventListener("DOMContentLoaded", function () {
        document
            .querySelectorAll(".wqpn-wishlist-button")
            .forEach(function (button) {
                button.addEventListener("click", function () {
                    var productId = this.dataset.productId;
                    var nonce = this.dataset.nonce;
                    var wishlistAction = this.classList.contains(
                        "wqpn-wishlist-full"
                    )
                        ? "remove_from_wishlist"
                        : "add_to_wishlist";
                    var action = "click_wishlist_button";
                    var url = this.dataset.url;
                    var data = {
                        action: action,
                        product_id: productId,
                        _wpnonce: nonce,
                        wishlist_action: wishlistAction,
                    };
                    // Add loading icon
                    this.innerHTML =
                        //'<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
                        '<div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>';

                    this.disabled = true;

                    console.log("button clicked Request Data:", data);
                    console.dir(this);
                    console.log(this);
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: data,
                        success: function (response) {
                            try {
                                var responseData = JSON.parse(response);
                                this.innerHTML =
                                    wishlistAction === "remove_from_wishlist"
                                        ? "Remove from Wishlist"
                                        : "Add to Wishlist";
                                this.disabled = false;
                                wishlist_callback(responseData);
                            } catch (err) {
                                console.warn(
                                    "Error parsing JSON response:",
                                    err
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(
                                "AJAX request failed:",
                                status,
                                error
                            );
                        },
                    });
                });
            });
    });
    // Remove button handler for wishlist page
    document
        .querySelectorAll(".wqpn-wishlish-page-remove")
        .forEach(function (button) {
            button.addEventListener("click", function () {
                var productId = this.dataset.productId;
                var nonce = this.dataset.nonce;
                var action = "click_wishlist_button";
                var wishlistAction = "remove_from_wishlist";
                var url = this.dataset.url;
                var data = {
                    action: action,
                    product_id: productId,
                    _wpnonce: nonce,
                    wishlist_action: wishlistAction,
                };
                console.log("Request Data:", data);
                // Add loading icon
                // this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                // this.disabled = true;
                $.ajax({
                    url: url,
                    method: "POST",
                    data: data,
                    success: function (response) {
                        try {
                            var responseData = JSON.parse(response);
                            console.log("Response Data:", responseData);
                            // button.innerHTML =
                            //     wishlistAction === "remove_from_wishlist"
                            //         ? "Remove from Wishlist"
                            //         : "Add to Wishlist";
                            // button.disabled = false;
                            wishlist_page_callback(responseData);
                        } catch (err) {
                            console.warn("Error parsing JSON response:", err);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX request failed:", status, error);
                    },
                });
            });
        });

    // handleSubmitPriceClick();
})(jQuery);
