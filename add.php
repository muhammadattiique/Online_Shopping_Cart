<?php
session_start();
include 'db.php'; // connect to database

$name = $_GET['name'];
$price = $_GET['price'];
$image = $_GET['image'] ?? 'default.jpg'; 

// 1️⃣ Update PHP session
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach($_SESSION['cart'] as &$item) {
    if($item['name'] == $name) {
        $item['quantity'] += 1;
        $found = true;
        break;
    }
}

if(!$found) {
    $_SESSION['cart'][] = [
        'name' => $name,
        'price' => $price,
        'quantity' => 1,
        'image' => $image
    ];
}

// 2️⃣ Update database
// Try updating first
$update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE name = ?");
$update->bind_param("s", $name);
$update->execute();

// If no rows were affected, insert new
if($update->affected_rows == 0) {
    $quantity = 1;
    $insert = $conn->prepare("INSERT INTO cart (name, price, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("sdi", $name, $price, $quantity);
    $insert->execute();
}

// Redirect back
header("Location: index.php");
exit;
?>
