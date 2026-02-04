<?php
session_start();
require_once('Minionshoesconfig.php');

// 1. Calculate Totals (Synchronized with cart.php logic)
$subtotal = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) { 
        $subtotal += $item['price'] * $item['quantity']; 
    }
} else { 
    header("Location: cart.php"); 
    exit(); 
}

// Math logic from cart.php
$tax = $subtotal * 0.06;
$shipping = ($subtotal > 200) ? 0 : 15.00;
$grandTotal = $subtotal + $tax + $shipping;

// 2. Process Order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = htmlspecialchars($_POST['name']); 
    $customer_email = $_SESSION['email'] ?? 'guest@example.com';
    $address = htmlspecialchars($_POST['address']);
    $payment = $_POST['payment'];

    // INSERT includes the final grand total (Price + Tax + Shipping)
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $customer_name, $customer_email, $grandTotal, $address, $payment);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        foreach ($_SESSION['cart'] as $item) {
            $det_stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
            $det_stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $det_stmt->execute();
        }

        unset($_SESSION['cart']);
        $orderSuccess = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background:#f9f9f9; padding: 40px; }
        .container { max-width: 500px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-pay { background:#111; color:white; width:100%; padding:14px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; }
        .btn-pay:hover { background:#ff6b6b; }
    </style>
</head>
<body>

<div class="container">
    <?php if (isset($orderSuccess)): ?>
        <div class="text-center">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h2>Order Success!</h2>
            <p>Your order <strong>#ORD-<?= $order_id ?></strong> is placed.</p>
            <a href="homeindex.php" class="btn btn-outline-dark w-100 mt-3">Back Home</a>
        </div>
    <?php else: ?>
        <h2 class="mb-4">Finalize Order</h2>
        <form method="POST">
            <label class="fw-bold small mb-1">Recipient Name</label>
            <input type="text" name="name" class="form-control mb-3" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>

            <label class="fw-bold small mb-1">Delivery Address</label>
            <input type="text" name="address" class="form-control mb-3" placeholder="Street, City, Postcode" required>

            <label class="fw-bold small mb-1">Payment Method</label>
            <select name="payment" class="form-select mb-4">
                <option value="Credit Card">Credit Card</option>
                <option value="Online Banking">Online Banking</option>
            </select>

            <button type="submit" class="btn-pay">Confirm & Pay RM <?= number_format($grandTotal, 2) ?></button>
        </form>
    <?php endif; ?>
</div>

</body>

</html>
