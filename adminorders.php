<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle Status Update
if (isset($_GET['update_id']) && isset($_GET['new_status'])) {
    $id = intval($_GET['update_id']);
    $status = $conn->real_escape_string($_GET['new_status']);
    $conn->query("UPDATE orders SET status = '$status' WHERE order_id = $id");
    
    // FIXED: Redirect back to this same file (adminorders.php)
    header("Location: adminorders.php?status_updated=1");
    exit();
}

// 3. Fetch Stats
$pendingCount = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Pending'")->fetch_assoc()['total'];
$totalCount   = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$revenue      = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'Cancelled'")->fetch_assoc()['total'];

// 4. Fetch Orders based on Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
$sql = "SELECT * FROM orders";
if ($filter !== 'All') {
    $sql .= " WHERE status = '$filter'";
}
$sql .= " ORDER BY order_date DESC";
$ordersResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Orders | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; display: flex; min-height: 100vh; overflow-x: hidden; }
        
        /* --- SIDEBAR --- */
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; position: fixed; height: 100%; z-index: 100; }
        .brand { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 40px; letter-spacing: 1px; color: wheat; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: block; border-radius: 6px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid yellow; }

        /* --- MAIN CONTENT --- */
        .main-content { flex: 1; margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header-title { font-weight: 800; color: #333; margin-bottom: 20px; }

        /* --- STATS CARDS --- */
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        .stat-card { flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #ccc; }
        .stat-card h3 { font-size: 2rem; margin: 0; font-weight: bold; }
        .stat-card p { margin: 0; color: #777; font-size: 0.9rem; }
        .border-blue { border-color: #3498db; }
        .border-green { border-color: #2ecc71; }
        .border-orange { border-color: #f39c12; }

        /* --- FILTERS --- */
        .filter-btn {
            border: none; background: white; padding: 10px 20px; border-radius: 30px;
            margin-right: 10px; font-weight: 600; color: #555; transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-decoration: none; display: inline-block;
        }
        .filter-btn:hover, .filter-btn.active { background: #1a1a1a; color: white; }

        /* --- TABLE --- */
        .table-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); margin-top: 20px; }
        .table th { border-top: none; color: #777; font-weight: 600; font-size: 0.9rem; white-space: nowrap; }
        .table td { vertical-align: middle; white-space: nowrap; }

        /* --- STATUS BADGES --- */
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }
        .status-Pending { background-color: #fff3cd; color: #856404; }
        .status-Shipped { background-color: #d4edda; color: #155724; }
        .status-Cancelled { background-color: #f8d7da; color: #721c24; }

        /* --- ACTION BUTTONS --- */
        .btn-action { padding: 5px 10px; font-size: 0.85rem; border-radius: 5px; border: none; cursor: pointer; transition: 0.2s; color: white; margin-right: 3px; display: inline-block; }
        .btn-view { background-color: #3498db; }
        .btn-ship { background-color: #2ecc71; }
        .btn-cancel { background-color: #e74c3c; }
        .btn-action:hover { opacity: 0.8; color: white; }

        /* --- TOAST --- */
        .toast-notification {
            position: fixed; bottom: 20px; right: 20px; background: #333; color: white;
            padding: 15px 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: translateY(100px); transition: 0.3s; z-index: 1050; opacity: 0;
        }
        .toast-notification.show { transform: translateY(0); opacity: 1; }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 70px; padding: 10px; }
            .sidebar .brand, .sidebar a span { display: none; }
            .sidebar a { text-align: center; padding: 15px 5px; }
            .main-content { margin-left: 70px; width: calc(100% - 70px); }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="admindashboard.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a>
        <a href="adminmanagecustomer.php"><i class="fa-solid fa-users"></i> <span>Customers</span></a>
        <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> <span>Categories</span></a>
        <a href="adminmanageproduct.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
        <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="adminlogin.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <div class="main-content">
        <h2 class="header-title">Order Management</h2>

        <div class="stats-container">
            <div class="stat-card border-orange">
                <h3><?php echo $pendingCount; ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="stat-card border-green">
                <h3>RM <?php echo number_format($revenue, 2); ?></h3>
                <p>Total Revenue (Active)</p>
            </div>
            <div class="stat-card border-blue">
                <h3><?php echo $totalCount; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>

        <div class="d-flex mb-3 flex-wrap gap-2">
            <a href="adminorders.php?filter=All" class="filter-btn <?php echo $filter == 'All' ? 'active' : ''; ?>">All Orders</a>
            <a href="adminorders.php?filter=Pending" class="filter-btn <?php echo $filter == 'Pending' ? 'active' : ''; ?>">Pending</a>
            <a href="adminorders.php?filter=Shipped" class="filter-btn <?php echo $filter == 'Shipped' ? 'active' : ''; ?>">Shipped</a>
            <a href="adminorders.php?filter=Cancelled" class="filter-btn <?php echo $filter == 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($ordersResult->num_rows > 0):
                            while($order = $ordersResult->fetch_assoc()): 
                                // Prepare data attributes for JS
                                $orderIdStr = "#ORD-" . str_pad($order['order_id'], 3, '0', STR_PAD_LEFT);
                        ?>
                        <tr>
                            <td><strong><?php echo $orderIdStr; ?></strong></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                            <td>RM <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><span class="badge-status status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                            <td>
                                <button class="btn-action btn-view" 
                                    onclick="viewOrder(
                                        '<?php echo $orderIdStr; ?>', 
                                        '<?php echo htmlspecialchars($order['customer_name']); ?>',
                                        '<?php echo $order['status']; ?>',
                                        '<?php echo number_format($order['total_amount'], 2); ?>'
                                    )">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <?php if($order['status'] == 'Pending'): ?>
                                    <a href="adminorders.php?update_id=<?php echo $order['order_id']; ?>&new_status=Shipped" class="btn-action btn-ship"><i class="fas fa-shipping-fast"></i></a>
                                    <a href="adminorders.php?update_id=<?php echo $order['order_id']; ?>&new_status=Cancelled" class="btn-action btn-cancel" onclick="return confirm('Cancel this order?')"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                            <tr><td colspan="6" class="text-center p-3">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fa-solid fa-box-open fa-3x text-primary mb-2"></i>
                        <h4 id="modalOrderId">#ORD-000</h4>
                        <span id="modalStatus" class="badge bg-secondary">Status</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="fw-bold">Customer:</span>
                        <span id="modalCustomer">Name</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="fw-bold">Total Amount:</span>
                        <span id="modalTotal">RM 0.00</span>
                    </div>
                    <div class="alert alert-info mt-3">
                        <small><i class="fas fa-info-circle"></i> Complete item details fetch requires AJAX (not implemented in this demo).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" class="toast-notification">
        <i class="fas fa-check-circle me-2"></i> Order status updated successfully!
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- A. Handle Toast Notification ---
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('status_updated')) {
            const toast = document.getElementById('toast');
            setTimeout(() => {
                toast.classList.add('show');
            }, 500); 

            setTimeout(() => {
                toast.classList.remove('show');
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({path:newUrl},'',newUrl);
            }, 3500);
        }

        // --- B. Handle View Order Modal ---
        function viewOrder(orderId, customerName, status, total) {
            document.getElementById('modalOrderId').innerText = orderId;
            document.getElementById('modalCustomer').innerText = customerName;
            document.getElementById('modalTotal').innerText = 'RM ' + total;
            
            const statusBadge = document.getElementById('modalStatus');
            statusBadge.innerText = status;
            statusBadge.className = 'badge'; 
            
            if(status === 'Pending') statusBadge.classList.add('bg-warning', 'text-dark');
            else if(status === 'Shipped') statusBadge.classList.add('bg-success');
            else if(status === 'Cancelled') statusBadge.classList.add('bg-danger');
            else statusBadge.classList.add('bg-secondary');

            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
            orderModal.show();
        }
    </script>

</body>
</html>