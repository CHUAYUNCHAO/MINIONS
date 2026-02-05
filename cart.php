<?php
session_start();
require_once 'Minionshoesconfig.php';

// Redirect guests to login page immediately
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='custloginandregister.php';</script>";
    exit();
}
// Calculate Totals
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
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; color: #333; }
        #loadingOverlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; display: none; justify-content: center; align-items: center; }
        .cart-container { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .cart-item { display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; padding: 25px 0; }
        .cart-item:last-child { border-bottom: none; }
        .btn-checkout { background: #111; color: white; padding: 15px; border-radius: 10px; width: 100%; border: none; font-weight: 700; transition: 0.3s; }
        .btn-checkout:hover { background: #ff6b6b; color: white; }
    </style>
</head>
<body>

    <div id="loadingOverlay">
        <div class="spinner-border text-dark" role="status"></div>
    </div>

    <header style="background:white; padding:20px 40px; display:flex; justify-content:space-between; align-items:center;">
        <div style="font-size:1.8rem; font-weight:800;">🍌 MINION SHOE</div>
        <nav class="d-none d-md-flex" style="gap:25px;">
            <a href="homeindex.php" style="text-decoration:none; color:#555; font-weight:600;">Home</a>
            <a href="catelouge.php" style="text-decoration:none; color:#555; font-weight:600;">Shop</a>
            <a href="cart.php" style="text-decoration:none; color:#ff6b6b; font-weight:600;">Cart</a>
        </nav>
    </header>
    
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Your Selection</h2>
            <a href="catelouge.php" class="text-dark text-decoration-none fw-bold"><i class="fas fa-arrow-left me-1"></i> Continue Shopping</a>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-container">
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                            <div class="cart-item">
                                <img src="<?= htmlspecialchars($item['image']) ?>" width="100" class="me-4 rounded">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h5 class="fw-bold"><?= htmlspecialchars($item['name']) ?></h5>
                                        <h5 class="fw-bold">RM <?= number_format($item['price'] * $item['quantity'], 2) ?></h5>
                                    </div>
                                    
                                    <div class="text-muted small mb-2">Size: <?= htmlspecialchars($item['size']) ?></div>

                                    <div class="d-flex align-items-center mb-3">
                                        <small class="text-muted me-2">Color:</small>
                                        <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold" 
                                                onchange="updateCart('<?= $index ?>', 'color', this.value)">
                                            <?php 
                                            // 1. Get the raw string "black,red,white" from session
                                            $rawColors = $item['all_colors'] ?? 'Standard';
                                            
                                            // 2. Convert to array
                                            $availableColors = explode(',', $rawColors);
                                            
                                            // 3. Loop and create options
                                            foreach($availableColors as $c): 
                                                $c = trim($c); // Remove extra spaces
                                                $selected = ($item['selected_color'] == $c) ? 'selected' : '';
                                            ?>
                                                <option value="<?= $c ?>" <?= $selected ?>><?= ucfirst($c) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <button class="btn btn-outline-dark" onclick="updateCart('<?= $index ?>', 'quantity', <?= $item['quantity'] - 1 ?>)">-</button>
                                            <input type="text" class="form-control text-center fw-bold bg-white" value="<?= $item['quantity'] ?>" readonly>
                                            <button class="btn btn-outline-dark" onclick="updateCart('<?= $index ?>', 'quantity', <?= $item['quantity'] + 1 ?>)">+</button>
                                        </div>
                                        <button class="btn btn-link text-danger text-decoration-none small p-0" onclick="updateCart('<?= $index ?>', 'remove', 0)">
                                            <i class="fas fa-trash-alt me-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                            <h4 class="text-muted">Your cart is empty</h4>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white p-4 rounded shadow-sm">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>RM <?= number_format($subtotal, 2) ?></span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Tax (6%)</span><span>RM <?= number_format($tax, 2) ?></span></div>
                    <div class="d-flex justify-content-between mb-4"><span>Shipping</span><span>RM <?= number_format($shipping, 2) ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4"><span class="fw-bold">Total</span><span class="fw-bold text-danger">RM <?= number_format($total, 2) ?></span></div>
                    <button class="btn-checkout" onclick="location.href='checkout.php'">CHECKOUT NOW</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCart(index, action, value) {
            if (action === 'quantity' && value < 1) return;
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            fetch(`updatecart.php?index=${index}&action=${action}&value=${value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert('Error updating cart');
                });
        }
    </script>
</body>
</html>