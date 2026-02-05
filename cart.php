<?php
session_start();
require_once 'Minionshoesconfig.php';

// --- SECURITY: REDIRECT IF NOT LOGGED IN ---
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please log in to view your cart.');
        window.location.href = 'custloginandregister.php';
    </script>";
    exit();
}

// 1. Calculate Totals
$subtotal = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}
$tax = $subtotal * 0.06;
$shipping = ($subtotal > 200 || $subtotal == 0) ? 0 : 15.00;
$total = $subtotal + $tax + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Cart | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Your Cart</h1>
        <p>Welcome back, User #<?= $_SESSION['user_id'] ?></p>
        </div>
</body>
</html>