<?php
session_start();

// Remove item from cart
if (isset($_GET['remove'])) {
    $key = $_GET['remove'];
    if (isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
    }
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<link rel="stylesheet" href="cart_style.css">
</head>
<body>

<header>
  <h1>Your Shopping Cart</h1>
  <p>Review your items before checkout</p>
</header>

<div class="cart-container">

<?php if (empty($_SESSION['cart'])): ?>

  <p class="empty-cart">Your cart is empty.</p>
  <div style="text-align:center;">
      <a href="index.php" class="btn-back">Continue Shopping</a>
  </div>

<?php else: ?>

<form method="post" action="checkout.php">

<table class="cart-table">
  <thead>
    <tr>
      <th>Product</th>
      <th>Price (₨)</th>
      <th>Quantity</th>
      <th>Total (₨)</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>

<?php
$grandTotal = 0;
foreach ($_SESSION['cart'] as $key => $item):
    $total = $item['price'] * $item['quantity'];
    $grandTotal += $total;
?>

<tr>
  <td class="product-info">
    <img src="images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
    <span><?php echo $item['name']; ?></span>
  </td>

  <td><?php echo $item['price']; ?></td>

  <td>
    <div class="quantity-control">
      <button type="button" onclick="updateQty(<?php echo $key; ?>, -1)">−</button>
      <input type="text" value="<?php echo $item['quantity']; ?>" readonly>
      <button type="button" onclick="updateQty(<?php echo $key; ?>, 1)">+</button>
    </div>
  </td>

  <td><?php echo $total; ?></td>

  <td>
    <a href="cart.php?remove=<?php echo $key; ?>" class="remove-btn">Remove</a>
  </td>
</tr>

<?php endforeach; ?>

  </tbody>
</table>

<div class="cart-summary">
  <h3>Grand Total: ₨<?php echo $grandTotal; ?></h3>

  <a href="index.php" class="btn-back">Continue Shopping</a>

  <button type="submit" name="confirm_order" class="checkout-btn">
      Confirm Order
  </button>
</div>

</form>

<?php endif; ?>

</div>

<script>
function updateQty(key, change) {
    window.location.href = "qty.php?key=" + key + "&val=" + change;
}
</script>

</body>
</html>
