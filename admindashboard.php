<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Fetch Key Statistics
// A. Total Revenue (from completed/shipped orders usually, but taking all for now)
$revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'Cancelled'")->fetch_assoc()['total'];

// B. Total Customers
$userCount = $conn->query("SELECT COUNT(*) as total FROM addusers")->fetch_assoc()['total'];

// C. Total Orders
$orderCount = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];

// D. Low Stock Warning (Products with stock < 10)
$lowStock = $conn->query("SELECT COUNT(*) as total FROM allproducts WHERE stock < 10")->fetch_assoc()['total'];

// 3. Fetch Recent Orders (More useful than just customers)
$recentOrders = $conn->query("
    SELECT o.order_id, o.customer_name, o.total_amount, o.status, o.order_date 
    FROM orders o 
    ORDER BY o.order_date DESC LIMIT 5
");

// 4. Fetch Monthly Sales Data for Chart (Simple Group By)
// This query groups sales by date (last 7 days)
$chartQuery = "SELECT DATE(order_date) as date, SUM(total_amount) as daily_total 
               FROM orders 
               WHERE status != 'Cancelled' 
               GROUP BY DATE(order_date) 
               ORDER BY date ASC LIMIT 7";
$chartResult = $conn->query($chartQuery);

$dates = [];
$sales = [];
while($row = $chartResult->fetch_assoc()) {
    $dates[] = date('d M', strtotime($row['date']));
    $sales[] = $row['daily_total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Minion Shoe</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f4f6f9; overflow-x: hidden; }
        
        /* Sidebar (Fixed Layout) */
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; flex-shrink: 0; }
        .brand { font-size: 1.5rem; font-weight: 800; text-align: center; margin-bottom: 40px; color: wheat; letter-spacing: 1px; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid #ffe600; }
        
        /* Main Content */
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header-title { font-size: 1.8rem; font-weight: 800; color: #333; margin-bottom: 25px; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: start; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-info h3 { margin: 0; font-size: 0.9em; color: #888; text-transform: uppercase; font-weight: 700; }
        .stat-info p { margin: 10px 0 0; font-size: 1.8em; font-weight: 800; color: #1a1a1a; }
        .icon-box { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        
        /* Color themes for cards */
        .card-yellow .icon-box { background: #fff9c4; color: #fbc02d; }
        .card-blue .icon-box { background: #e3f2fd; color: #1976d2; }
        .card-green .icon-box { background: #e8f5e9; color: #2e7d32; }
        .card-red .icon-box { background: #ffebee; color: #c62828; }

        /* Charts & Tables Area */
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-header h3 { margin: 0; font-size: 1.1rem; color: #333; font-weight: 700; }

        /* Table Styling */
        .table { width: 100%; border-collapse: collapse; }
        .table td { padding: 12px 0; border-bottom: 1px solid #f1f1f1; color: #555; font-size: 0.95rem; }
        .table th { text-align: left; color: #888; font-size: 0.85rem; padding-bottom: 10px; }
        
        /* Status Badges */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .status-Pending { background: #fff3cd; color: #856404; }
        .status-Shipped { background: #d1e7dd; color: #0f5132; }
        .status-Cancelled { background: #f8d7da; color: #721c24; }

        @media (max-width: 1000px) { .dashboard-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <div style="flex-grow: 1;">
            <a href="admindashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="adminmanagecustomer.php"><i class="fas fa-users"></i> Customers</a>
            <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
            <a href="adminmanageproduct.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
            <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        </div>
        <a href="adminlogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2 class="header-title">Dashboard Overview</h2>

        <div class="stats-grid">
            <div class="stat-card card-green">
                <div class="stat-info">
                    <h3>Total Revenue</h3>
                    <p>RM <?php echo number_format($revenue, 2); ?></p>
                </div>
                <div class="icon-box"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="stat-card card-blue">
                <div class="stat-info">
                    <h3>Total Orders</h3>
                    <p><?php echo $orderCount; ?></p>
                </div>
                <div class="icon-box"><i class="fas fa-shopping-bag"></i></div>
            </div>
            <div class="stat-card card-yellow">
                <div class="stat-info">
                    <h3>Customers</h3>
                    <p><?php echo $userCount; ?></p>
                </div>
                <div class="icon-box"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-card card-red">
                <div class="stat-info">
                    <h3>Low Stock Alert</h3>
                    <p><?php echo $lowStock; ?></p>
                </div>
                <div class="icon-box"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>

        <div class="dashboard-grid">
            
            <div class="card">
                <div class="card-header">
                    <h3>Sales Analytics (Last 7 Days)</h3>
                    <select style="border:none; background:#f4f4f4; padding:5px; border-radius:5px;">
                        <option>This Week</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Recent Orders</h3>
                    <a href="adminorders.php" style="text-decoration:none; font-size:0.85rem; color:#ffe600;">View All</a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($recentOrders->num_rows > 0):
                            while($order = $recentOrders->fetch_assoc()): 
                        ?>
                        <tr>
                            <td>
                                <strong style="color:#333;">#ORD-<?php echo str_pad($order['order_id'], 3, '0', STR_PAD_LEFT); ?></strong><br>
                                <span style="font-size:0.8em;"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                            </td>
                            <td style="font-weight:bold;">RM <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><span class="badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                            echo "<tr><td colspan='3' style='text-align:center;'>No recent orders</td></tr>";
                        endif; 
                        ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button onclick="location.href='adminmanageproduct.php'" style="width:100%; padding:12px; background:#1a1a1a; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">
                        <i class="fas fa-plus me-2"></i> Manage Inventory
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Data from PHP
        const labels = <?php echo json_encode($dates); ?>;
        const dataPoints = <?php echo json_encode($sales); ?>;

        // If no data, provide dummy data for visual check
        const chartLabels = labels.length > 0 ? labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const chartData = dataPoints.length > 0 ? dataPoints : [0, 0, 0, 0, 0, 0, 0];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Revenue (RM)',
                    data: chartData,
                    borderColor: '#ffe600',
                    backgroundColor: 'rgba(255, 230, 0, 0.1)',
                    borderWidth: 3,
                    tension: 0.4, // Smooth curve
                    fill: true,
                    pointBackgroundColor: '#1a1a1a',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>

</body>
</html>