<?php
session_start();
require_once('Minionshoesconfig.php'); // Ensure this file exists and connects to DB

// 1. Security: Redirect if Cart is Empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// 2. Calculate Totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$tax = $subtotal * 0.06; // 6% Tax
$shipping = ($subtotal > 200) ? 0 : 15.00; // Free shipping over RM200
$grandTotal = $subtotal + $tax + $shipping;

$orderSuccess = false;
$order_id = 0;

// 3. Process Order Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name  = htmlspecialchars($_POST['name']);
    $customer_email = $_SESSION['email'] ?? htmlspecialchars($_POST['email']);
    
    // --- POSTCODE CLEANING (Backend Security) ---
    // This removes any non-number characters on the server side just in case
    $clean_zip = preg_replace('/[^0-9]/', '', $_POST['zip']); 
    
    $address = htmlspecialchars($_POST['address']) . ", " . htmlspecialchars($_POST['city']) . ", " . $clean_zip;
    $payment = htmlspecialchars($_POST['payment']);
    $order_date = date('Y-m-d H:i:s');
    $status = 'Pending';

    // A. Insert into 'orders' table
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, total_amount, shipping_address, payment_method, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssss", $customer_name, $customer_email, $grandTotal, $address, $payment, $order_date, $status);

    if ($stmt->execute()) {
        $order_id = $conn->insert_id; // Get the ID of the new order

        // B. Insert items into 'order_items' table (Optional/Advanced)
        // If you have an 'order_items' table, uncomment the logic below:
        /*
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $item_stmt->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['quantity']);
            $item_stmt->execute();
        }
        */

        // C. Clear Cart & Show Success
        unset($_SESSION['cart']);
        $orderSuccess = true;
    } else {
        $error = "Error placing order: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; padding-bottom: 40px; }
        
        /* Header Style */
        header { background: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 40px; }
        .brand { font-size: 1.8rem; font-weight: 800; letter-spacing: 2px; color: #111; text-decoration: none; }
        
        .checkout-container { max-width: 960px; margin: 0 auto; padding: 0 20px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { background: white; border-bottom: 1px solid #eee; padding: 20px; font-weight: 800; font-size: 1.2rem; color: #333; }
        
        /* Summary Section */
        .summary-card { background-color: #1a1a1a; color: white; border-radius: 15px; padding: 30px; height: 100%; }
        .summary-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; color: wheat; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.95rem; color: #ccc; }
        .summary-item.total { border-top: 1px solid #444; margin-top: 20px; padding-top: 20px; font-size: 1.2rem; color: white; font-weight: 800; }
        
        /* Form Section */
        .form-label { font-weight: 600; font-size: 0.9rem; color: #555; }
        .form-control, .form-select { padding: 12px; border-radius: 8px; border: 1px solid #ddd; }
        .form-control:focus { border-color: wheat; box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25); }
        
        .btn-pay { background: wheat; color: black; width: 100%; padding: 15px; border: none; border-radius: 8px; font-weight: 800; font-size: 1rem; transition: 0.3s; margin-top: 20px; }
        .btn-pay:hover { background: #ffeb3b; transform: translateY(-2px); }
        
        /* Success Screen */
        .success-box { text-align: center; padding: 50px; }
        .icon-circle { width: 80px; height: 80px; background: #e8f5e9; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <header>
        <a href="homeindex.php" class="brand">🍌 MINION SHOE</a>
        <a href="cart.php" class="text-dark fw-bold text-decoration-none"><i class="fas fa-arrow-left me-2"></i> Back to Cart</a>
    </header>

    <div class="checkout-container">
        
        <?php if ($orderSuccess): ?>
            <div class="card success-box">
                <div>
                    <div class="icon-circle">
                        <i class="fas fa-check fa-3x text-success"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Order Placed Successfully!</h2>
                    <p class="text-muted mb-4">Thank you for shopping with Minion Shoe. Your order number is <strong>#ORD-<?= str_pad($order_id, 4, '0', STR_PAD_LEFT) ?></strong>.</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="catelouge.php" class="btn btn-outline-dark px-4 py-2">Continue Shopping</a>
                        <a href="profile.php" class="btn btn-dark px-4 py-2">View Order</a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="row g-4">
                
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header"><i class="fas fa-shipping-fast me-2"></i> Shipping Details</div>
                        <div class="card-body p-4">
                            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                            <form id="checkoutForm" method="POST" onsubmit="return startLoading()">
                                
                                <h6 class="mb-3 text-uppercase text-muted fw-bold" style="font-size: 0.8rem;">Contact Info</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <h6 class="mb-3 text-uppercase text-muted fw-bold" style="font-size: 0.8rem;">Shipping Address</h6>
                                <div class="mb-3">
                                    <label class="form-label">Street Address</label>
                                    <input type="text" name="address" class="form-control" placeholder="123 Banana Street" required>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Postcode / Zip</label>
                                        <input type="text" 
                                               name="zip" 
                                               class="form-control" 
                                               pattern="[0-9]*" 
                                               inputmode="numeric" 
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                               placeholder="e.g. 75450" 
                                               maxlength="5" 
                                               required>
                                    </div>
                                </div>

                                <h6 class="mb-3 text-uppercase text-muted fw-bold" style="font-size: 0.8rem;">Payment</h6>
                                <div class="mb-3">
                                    <select name="payment" class="form-select">
                                        <option value="Credit Card">Credit / Debit Card</option>
                                        <option value="Online Banking">Online Banking (FPX)</option>
                                        <option value="E-Wallet">Touch 'n Go / GrabPay</option>
                                    </select>
                                </div>

                                <button type="submit" id="payBtn" class="btn-pay">
                                    <span>Pay RM <?= number_format($grandTotal, 2) ?></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="summary-card">
                        <div class="summary-title">Order Summary</div>
                        
                        <div style="max-height: 250px; overflow-y: auto; margin-bottom: 20px; padding-right: 5px;">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="background: white; padding: 5px; border-radius: 5px;">
                                        <i class="fas fa-shoe-prints text-dark fa-lg"></i>
                                    </div>
                                    <div>
                                        <div style="color:white; font-weight:600;"><?= htmlspecialchars($item['name']) ?></div>
                                        <small style="color:#888;">Qty: <?= $item['quantity'] ?> x RM<?= $item['price'] ?></small>
                                    </div>
                                </div>
                                <div style="color:white; font-weight:600;">RM <?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div style="border-top: 1px solid #444; padding-top: 20px;">
                            <div class="summary-item">
                                <span>Subtotal</span>
                                <span>RM <?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Tax (6%)</span>
                                <span>RM <?= number_format($tax, 2) ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Shipping</span>
                                <span><?= ($shipping == 0) ? 'Free' : 'RM ' . number_format($shipping, 2) ?></span>
                            </div>
                            <div class="summary-item total">
                                <span>Total</span>
                                <span style="color: #ff6b6b;">RM <?= number_format($grandTotal, 2) ?></span>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center small text-muted">
                            <i class="fas fa-lock me-1"></i> Secure Checkout
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </div>

    <script>
        function startLoading() {
            const btn = document.getElementById('payBtn');
            const span = btn.querySelector('span');
            
            btn.disabled = true;
            btn.style.opacity = '0.8';
            span.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            
            return true;
        }
    </script>

</body>
</html>