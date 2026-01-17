<?php
include 'db.php';

$id   = $_GET['id'];
$type = $_GET['type'];

if ($type == "inc") {
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE id=$id");
}

if ($type == "dec") {
    mysqli_query(
        $conn,
        "UPDATE cart SET quantity = IF(quantity > 1, quantity - 1, 1) WHERE id=$id"
    );
}

header("Location: cart.php");
exit;
?>
