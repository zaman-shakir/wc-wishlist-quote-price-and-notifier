<?php
$wishlist = isset($_COOKIE['wqpn_wishlist']) ? json_decode(stripslashes($_COOKIE['wqpn_wishlist']), true) : [];