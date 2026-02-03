<?php
session_start();
require_once 'Minionshoesconfig.php';
include 'homeheader.php';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; color: #333; line-height: 1.6; }
        a { text-decoration: none; color: inherit; transition: 0.3s; }
        header { background: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .brand { font-size: 1.8rem; font-weight: 800; letter-spacing: 2px; color: #111; }
        nav { display: flex; gap: 25px; }
        nav a { font-weight: 600; color: #555; font-size: 0.95rem; }
        nav a:hover { color: #ff6b6b; }
        .nav-icons { display: flex; gap: 15px; align-items: center; }
        .btn-login, .btn-admin { background-color: #111; color: white; padding: 8px 20px; border-radius: 20px; font-size: 0.9rem; }
        .btn-login:hover, .btn-admin:hover { background-color: #ff6b6b; }

        :root { --accent: #ff6b6b; --dark: #111; }
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        
        .cart-container { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .cart-item { display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; padding: 25px 0; transition: 0.3s; }
        .cart-item:hover { background: #fafafa; }
        .cart-item:last-child { border-bottom: none; }
        
        .summary-box { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: sticky; top: 30px; }
        .btn-checkout { background: var(--dark); color: white; padding: 15px; border-radius: 10px; font-weight: 700; transition: 0.3s; }
        .btn-checkout:hover { background: var(--accent); transform: translateY(-2px); }
        
        /* Color selection indicator */
        .color-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; border: 1px solid #ddd; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0"><i class="fas fa-shopping-bag me-2"></i> Your Selection</h2>
            <a href="catelouge.php" class="text-dark text-decoration-none fw-bold"><i class="fas fa-arrow-left me-1"></i> Continue Shopping</a>
        </div>

        <div class="row">
          <div class="col-lg-8">
    <div class="cart-container">
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <div class="cart-item">
                    <img src="<?= htmlspecialchars($item['image'] ?? 'placeholder.jpg') ?>" width="110" class="me-4 rounded shadow-sm">
                    
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-2"><?= htmlspecialchars($item['name'] ?? 'Unknown Product') ?></h5>
                        
                        <div class="mb-3 d-flex align-items-center">
                            <small class="text-muted me-2">Color:</small>
                            <select class="form-select form-select-sm w-auto" onchange="updateCart(<?= $index ?>, 'color', this.value)">
                                <?php 
                                $colors = ['black', 'red', 'white', 'blue', 'grey', 'silver'];
                                // 3. 确保 colors 键名存在
                                $currentColor = $item['colors'] ?? 'black';
                                foreach($colors as $c): 
                                    $isSelected = ($currentColor == $c) ? 'selected' : '';
                                ?>
                                    <option value="<?= $c ?>" <?= $isSelected ?>><?= ucfirst($c) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <button class="btn btn-outline-dark" onclick="updateCart(<?= $index ?>, 'quantity', <?= $item['quantity'] - 1 ?>)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="text" class="form-control text-center fw-bold" value="<?= $item['quantity'] ?>" readonly>
                                <button class="btn btn-outline-dark" onclick="updateCart(<?= $index ?>, 'quantity', <?= $item['quantity'] + 1 ?>)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            
                            <div class="text-end">
                                <span class="text-muted small">Price: RM <?= number_format($item['price'], 2) ?></span>
                            </div>
                        </div>

                        <button class="btn btn-link text-danger text-decoration-none small mt-2 p-0" onclick="updateCart(<?= $index ?>, 'remove', 0)">
                            <i class="fas fa-trash-alt me-1"></i> Remove Item
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-light mb-3"></i>
                <p class="text-muted fs-5">Your cart is currently empty.</p>
            </div>
                   <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-box">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">RM <?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Service Tax (6%)</span>
                        <span class="fw-bold text-dark">RM <?= number_format($tax, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Shipping</span>
                        <span><?= $shipping == 0 ? '<span class="text-success fw-bold">FREE</span>' : 'RM '.number_format($shipping, 2) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fs-5 fw-bold">Grand Total</span>
                        <span class="fs-3 fw-bold text-danger">RM <?= number_format($total, 2) ?></span>
                    </div>
                    <button class="btn btn-checkout w-100" onclick="location.href='checkout.php'">
                        CHECKOUT NOW <i class="fas fa-chevron-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
      function updateCart(index, action, value) {
    // Safety check: don't let quantity go below 1
    if (action === 'quantity' && value < 1) return; 

    // Send the data to your logic handler
    fetch(`updatecart.php?index=${index}&action=${action}&value=${value}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh to show new math (Price * Quantity)
                location.reload(); 
            }
        });
}
    </script>
</body>
</html>