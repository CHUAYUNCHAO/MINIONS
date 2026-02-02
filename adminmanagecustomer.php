<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Fetch All Customers from 'addusers'
$query = "SELECT * FROM addusers ORDER BY created_at DESC";
$result = $conn->query($query);

// 3. Stats Calculation
$totalCustomers = $result->num_rows;
$vipQuery = "SELECT COUNT(*) as vips FROM addusers WHERE status = 'VIP'"; 
$vipCountResult = $conn->query($vipQuery);
$vipCount = ($vipCountResult) ? $vipCountResult->fetch_assoc()['vips'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Customer List | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f1f4f6; }
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 40px; color: wheat; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: block; border-radius: 6px; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid #dde400; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; flex: 1; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; color: #666; font-size: 0.9em; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75em; font-weight: bold; text-transform: uppercase; }
        .vip { background-color: #e3f2fd; color: #0d47a1; }
        .new { background-color: #e8f5e9; color: #1b5e20; }
        .regular { background-color: #f5f5f5; color: #666; }
        .add-btn { background-color: #dde400; color: black; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .action-btn { border: none; background: none; cursor: pointer; color: #666; font-size: 1rem; transition: 0.2s; margin-right: 10px; }
        .action-btn:hover { color: #000; transform: scale(1.1); }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(2px); }
        .modal-box { background: white; padding: 30px; border-radius: 10px; width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: fadeIn 0.3s ease; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; font-size: 0.9em; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-cancel { background: #eee; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .btn-save { background: #1a1a1a; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
         <a href="admindashboard.php" ><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="adminmanagecustomer.php"><i class="fa-solid fa-users"></i> Customers</a>
        <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
        <a href="adminmanageproduct.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
        <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="adminlogin.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <?php if(isset($_GET['status'])): ?>
            <?php if($_GET['status'] == 'success'): ?>
                <div style="background: #e8f5e9; color: #1b5e20; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c8e6c9;">
                    <i class="fa-solid fa-circle-check"></i> Customer added successfully!
                </div>
            <?php elseif($_GET['status'] == 'updated'): ?>
                <div style="background: #e3f2fd; color: #0d47a1; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #bbdefb;">
                    <i class="fa-solid fa-user-pen"></i> Customer updated successfully!
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="header">
            <h1>Customer Management</h1>
            <button class="add-btn" onclick="openAddModal()">+ Add Customer</button>
        </div>

        <div class="stats-row" style="display:flex; gap:20px; margin-bottom:30px;">
            <div class="stat-card">
                <h4>Total Registered</h4>
                <p><?php echo number_format($totalCustomers); ?></p>
            </div>
            <div class="stat-card">
                <h4>VIP Members</h4>
                <p><?php echo $vipCount; ?></p>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Preferred Size</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong><br>
                            <small style="color:#888">Joined: <?php echo date('M Y', strtotime($user['created_at'])); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['shoe_size'] ?: 'N/A'); ?></td>
                        <td>
                            <?php 
                                $status = $user['status'] ?? 'Regular'; 
                                $badgeClass = strtolower($status); 
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                        </td>
                        <td>
                            <button class="action-btn" onclick='openEditModal(<?php echo json_encode($user); ?>)'>
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="admindeleteuser.php?id=<?php echo $user['id']; ?>" class="action-btn" onclick="return confirm('Delete this customer?');">
                                <i class="fa-solid fa-trash" style="color: #d32f2f;"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-box">
            <h2 id="modalTitle">Add New Customer</h2>
            <form id="customerForm" action="adminaddcust.php" method="POST">
                <input type="hidden" name="user_id" id="userIdInput">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="custName" id="custName" required placeholder="e.g. Kevin Hart">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="custEmail" id="custEmail" required placeholder="kevin@example.com">
                </div>
                
                <div style="display:flex; gap:10px; margin-bottom:15px;">
                    <div style="flex:1">
                        <label>Size</label>
                        <input type="text" name="custSize" id="custSize" placeholder="US 9">
                    </div>
                    <div style="flex:1">
                        <label>Spent ($)</label>
                        <input type="number" step="0.01" name="custSpent" id="custSpent" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="custStatus" id="custStatus">
                        <option value="New">New</option>
                        <option value="VIP">VIP</option>
                        <option value="Regular">Regular</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" id="submitBtn" class="btn-save">Save Customer</button>
                </div>
            </form>
        </div>
    </div>

   <script>
        const modal = document.getElementById('modalOverlay');
        const form = document.getElementById('customerForm');

        // This function resets the modal to "Add" mode
        function openAddModal() {
            document.getElementById('modalTitle').innerText = "Add New Customer";
            document.getElementById('submitBtn').innerText = "Save Customer";
            form.action = "adminaddcust.php"; // Point to the Add script
            form.reset();
            document.getElementById('userIdInput').value = "";
            modal.style.display = 'flex';
        }

        // THIS IS THE BRIDGE: It populates the modal with existing user data
        function openEditModal(user) {
            document.getElementById('modalTitle').innerText = "Edit Customer Details";
            document.getElementById('submitBtn').innerText = "Update Changes";
            
            // Change Form Action to the Update script
            form.action = "adminupdatecust.php";
            
            // Map 'addusers' database columns to the modal inputs
            document.getElementById('userIdInput').value = user.id; 
            document.getElementById('custName').value = user.full_name; 
            document.getElementById('custEmail').value = user.email; 
            document.getElementById('custSize').value = user.shoe_size; 
            document.getElementById('custSpent').value = user.total_spent; 
            document.getElementById('custStatus').value = user.status; 
            modal.style.display = 'flex';
        }

        function closeModal() { modal.style.display = 'none'; }
        window.onclick = function(e) { if (e.target == modal) closeModal(); }
    </script>
</body>
</html>