<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Fetch Stats
$userCount = $conn->query("SELECT COUNT(*) as total FROM addusers")->fetch_assoc()['total'];
$catCount  = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
$vipCount  = $conn->query("SELECT COUNT(*) as total FROM addusers WHERE status = 'VIP'")->fetch_assoc()['total'];
$totalSpent = $conn->query("SELECT SUM(total_spent) as total FROM addusers")->fetch_assoc()['total'];

// 3. Fetch Recent Customers
$recentUsers = $conn->query("SELECT * FROM addusers ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f1f4f6; }
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 40px; color: wheat; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: block; border-radius: 6px; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid #dde400; }
        
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-bottom: 4px solid #dde400; }
        .stat-card h3 { margin: 0; font-size: 0.9em; color: #888; text-transform: uppercase; }
        .stat-card p { margin: 10px 0 0; font-size: 1.8em; font-weight: bold; color: #1a1a1a; }
        
        .dashboard-row { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .recent-list { border-collapse: collapse; width: 100%; margin-top: 15px; }
        .recent-list td { padding: 12px; border-bottom: 1px solid #f0f0f0; font-size: 0.9em; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="admindashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="adminmanagecustomer.php"><i class="fa-solid fa-users"></i> Customers</a>
        <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
        <a href="adminmanageproduct.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
        <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="adminlogin.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1>Welcome Back, Admin</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p>RM<?php echo number_format($totalSpent, 2); ?></p>
            </div>
            <div class="stat-card">
                <h3>Customers</h3>
                <p><?php echo $userCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>Collections</h3>
                <p><?php echo $catCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>VIP Loyalty</h3>
                <p><?php echo $vipCount; ?></p>
            </div>
        </div>

        <div class="dashboard-row">
            <div class="card">
                <h3>Newest Customers</h3>
                <table class="recent-list">
                    <?php while($user = $recentUsers->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="text-align:right; color:#888;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            
            <div class="card" style="background: #1a1a1a; color: white;">
                <h3 style="color: #dde400;">Quick Actions</h3>
                <p style="font-size: 0.85em; opacity: 0.8; margin-bottom: 20px;">Manage your store settings quickly.</p>
                <button onclick="window.location.href='adminmanagecustomer.php'" style="width:100%; padding:10px; margin-bottom:10px; cursor:pointer; background:#dde400; border:none; border-radius:5px; font-weight:bold;">+ Add Customer</button>
                <button onclick="window.location.href='adminmanagecategories.php'" style="width:100%; padding:10px; cursor:pointer; background:transparent; border:1px solid #dde400; color:#dde400; border-radius:5px;">Manage Categories</button>
            </div>
        </div>
    </div>

</body>
</html>