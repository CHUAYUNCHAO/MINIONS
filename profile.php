<?php
session_start();
require_once('userconfigdashboard.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch User Data
$user_res = $conn->query("SELECT full_name FROM registerusers WHERE id = $user_id");
$user_data = $user_res->fetch_assoc();
$user_name = $user_data['full_name'] ?? 'User';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f1f4f6; }
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 40px; color: #ff6b6b; } 
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: block; border-radius: 6px; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid #ff6b6b; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="user_dashboard.php" class="active"><i class="fa-solid fa-house-user"></i> Dashboard</a>
        <a href="shop.php"><i class="fa-solid fa-bag-shopping"></i> Shop Now</a>
        <a href="logout.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1>Welcome, <?= htmlspecialchars($user_name); ?>! 👋</h1>
        <div class="stats-row">
            <div class="stat-card"><h4>Orders</h4><p></p></div>
            <div class="stat-card"><h4>Wallet</h4><p>RM <?= number_format($user_data['wallet_points'] ?? 0, 2); ?></p></div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px;">
            <h3>Recent Orders</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 10px;">Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
                <?php while($order = $recent_orders->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">#ORD-<?= $order['id'] ?></td>
                    <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                    <td>RM <?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= $order['status'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>