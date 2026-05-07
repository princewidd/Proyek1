<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start ();
}

function getCart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function addToCart($menuId, $menuData) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$menuId])) {
        $_SESSION['cart'][$menuId]['qty']++;
    } else {
        $_SESSION['cart'][$menuId] = [
            'nama' => $menuData['nama'],
            'harga' => $menuData['harga'],
            'qty' => 1
        ];
    }
}

function updateCartItem($menuId, $qty) {
    if ($qty <= 0) {
        // Kalau qty 0 atau negatif, hapus item
        unset($_SESSION['cart'][$menuId]);
    } else {
        // Update qty
        $_SESSION['cart'][$menuId]['qty'] = $qty;
    }
}

function removeFromCart($menuId) {
    unset($_SESSION['cart'][$menuId]);
}

function clearCart() {
    unset($_SESSION['cart']);
}

function getCartCount() {
    $cart = getCart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['qty'];
    }
    return $total;
}
?>