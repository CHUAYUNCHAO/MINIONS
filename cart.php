<?php
session_start();
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
    <title>Cart | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .cart-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .cart-item { display: flex; align-items: center; border-bottom: 1px solid #eee; padding: 15px 0; }
        .summary-box { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 fw-bold">Your Cart</h2>
        <div class="row">
            <div class="col-lg-8">
                <div class="cart-container">
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="cart-item">
                                <img src="<?= $item['image'] ?>" width="80" class="me-3 rounded">
                                <div class="flex-grow-1">
                                    <h5 class="mb-0"><?= htmlspecialchars($item['name']) ?></h5>
                                    <p class="text-muted small">Qty: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="fw-bold">RM <?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center py-4">Your cart is empty.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="summary-box">
                    <h5>Summary</h5>
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span>RM <?= number_format($subtotal, 2) ?></span></div>
                    <div class="d-flex justify-content-between"><span>Shipping</span><span><?= $shipping == 0 ? 'FREE' : 'RM '.number_format($shipping, 2) ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-4"><span>Total</span><span class="text-danger">RM <?= number_format($total, 2) ?></span></div>
                    <button class="btn btn-dark w-100 mt-3 py-3" onclick="location.href='checkout.php'">CHECKOUT</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>