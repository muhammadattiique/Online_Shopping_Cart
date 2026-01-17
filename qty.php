<?php
session_start();

$key = $_GET['key'];
$val = $_GET['val'];

// Check item exists in session cart
if (isset($_SESSION['cart'][$key])) {

    // Update quantity
    $_SESSION['cart'][$key]['quantity'] += $val;

    // Prevent quantity from going below 1
    if ($_SESSION['cart'][$key]['quantity'] < 1) {
        $_SESSION['cart'][$key]['quantity'] = 1;
    }
}

// Redirect back to cart
header("Location: cart.php");
exit;
?>
