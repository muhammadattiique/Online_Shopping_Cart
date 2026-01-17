<?php
session_start();
include 'db.php'; // Make sure this connects to your database

// If cart is empty and no last order, redirect
if (empty($_SESSION['cart']) && empty($_SESSION['last_order'])) {
    header("Location: index.php");
    exit;
}

$grandTotal = 0;
$orderConfirmed = false;

// Handle Confirm Order from cart
if (isset($_POST['confirm_order']) && !empty($_SESSION['cart'])) {

    // Save cart items for invoice display
    $_SESSION['last_order'] = $_SESSION['cart'];

    // Insert each item into orders table
    foreach ($_SESSION['cart'] as $item) {
        $name     = $item['name'];
        $price    = $item['price'];
        $quantity = $item['quantity'];
        $total    = $price * $quantity;

        $stmt = $conn->prepare(
            "INSERT INTO orders (product_name, price, quantity, total)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("siii", $name, $price, $quantity, $total);
        $stmt->execute();
    }

    $_SESSION['order_saved'] = true;
    $orderConfirmed = true;

    // Clear the cart
    unset($_SESSION['cart']);
}

// If order was already saved before
elseif (!empty($_SESSION['order_saved'])) {
    $orderConfirmed = true;
}

// Determine which items to show
$itemsToShow = $orderConfirmed ? ($_SESSION['last_order'] ?? []) : ($_SESSION['cart'] ?? []);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice</title>
<link rel="stylesheet" href="checkout_style.css">
<style>
.invoice-container { max-width: 900px; margin: auto; padding: 20px; border: 1px solid #ccc; }
.invoice-container h1 { text-align: center; margin-bottom: 10px; }
.date { text-align: right; margin-bottom: 20px; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
.product img { width: 50px; height: 50px; object-fit: cover; margin-right: 10px; vertical-align: middle; }
.actions { margin-top: 20px; text-align: center; }
button { padding: 10px 20px; margin: 5px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px; }
button:hover { background: #0056b3; }
.grand { text-align: right; margin-top: 20px; font-size: 1.2em; font-weight: bold; }
</style>
</head>
<body>

<div class="invoice-container">

    <h1>Invoice / Receipt</h1>
    <p class="date">Date: <?= date("d M Y"); ?></p>

    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>

        <?php foreach ($itemsToShow as $item):
            $total = $item['price'] * $item['quantity'];
            $grandTotal += $total;
        ?>
        <tr>
            <td class="product">
                <?php if (!empty($item['image'])): ?>
                    <img src="images/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                <?php endif; ?>
                <?= htmlspecialchars($item['name']); ?>
            </td>
            <td>₨<?= number_format($item['price']); ?></td>
            <td><?= $item['quantity']; ?></td>
            <td>₨<?= number_format($total); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2 class="grand">Grand Total: ₨<?= number_format($grandTotal); ?></h2>

    <div class="actions">
        <!-- Only Print button after order confirmed -->
        <?php if ($orderConfirmed): ?>
            <p>Order confirmed! You cannot go back to the cart.</p>
            <button onclick="window.print()">Print Receipt</button>
        <?php else: ?>
            <!-- This part should never be seen if cart.php posts correctly -->
            <form method="post">
                <button type="submit" name="confirm_order">Confirm Order</button>
            </form>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
