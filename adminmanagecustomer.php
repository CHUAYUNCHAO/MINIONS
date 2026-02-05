<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle Status Toggle
if (isset($_GET['toggle_id']) && isset($_GET['new_status'])) {
    $id = intval($_GET['toggle_id']);
    $status = $_GET['new_status']; // 'Active' or 'Inactive'
    
    // Update the REGISTERUSERS table (the one used for login)
    $stmt = $conn->prepare("UPDATE registerusers SET account_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    header("Location: adminmanagecustomer.php?msg=updated");
    exit();
}

// 3. Fetch Customers
$query = "SELECT * FROM registerusers ORDER BY id DESC";
$result = $conn->query($query);
$totalCustomers = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f1f4f6; }
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; padding: 20px; display: flex; flex-direction: column; }
        .brand { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 40px; color: wheat; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: block; border-radius: 6px; }
        .sidebar a.active { background-color: #333; color: white; border-left: 4px solid #dde400; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        
        /* Status Badges */
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .status-Active { background: #e8f5e9; color: #2e7d32; }
        .status-Inactive { background: #ffebee; color: #c62828; }
        
        .action-btn { color: #555; margin-right: 10px; font-size: 1.1em; }
        .toggle-btn:hover { cursor: pointer; transform: scale(1.1); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="admindashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="adminmanagecustomer.php" class="active"><i class="fa-solid fa-users"></i> Customers</a>
        <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
        <a href="adminmanageproduct.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
        <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="adminlogin.php" style="margin-top:auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1>Customer Management</h1>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php 
                            $status = $row['account_status'] ?? 'Active';
                            $nextStatus = ($status == 'Active') ? 'Inactive' : 'Active';
                            $icon = ($status == 'Active') ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted';
                        ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><span class="badge status-<?= $status ?>"><?= $status ?></span></td>
                            <td>
                                <a href="adminmanagecustomer.php?toggle_id=<?= $row['id'] ?>&new_status=<?= $nextStatus ?>" 
                                   class="action-btn toggle-btn" 
                                   title="Switch to <?= $nextStatus ?>">
                                    <i class="fa-solid <?= $icon ?> fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>