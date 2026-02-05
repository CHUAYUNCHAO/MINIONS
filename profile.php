<?php
session_start();
require_once('Minionshoesconfig.php');

// 1. Security & Data Setup
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'] ?? ''; 
$message = ""; // Variable to store success/error messages

// --- NEW: Handle Profile Update Logic ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name = $conn->real_escape_string($_POST['full_name']);
    $new_password = $_POST['password'];

    // Update Query
    if (!empty($new_password)) {
        // Note: In a real app, you should hash passwords using password_hash()
        $sql = "UPDATE registerusers SET full_name = '$new_name', password = '$new_password' WHERE id = $user_id";
    } else {
        // If password field is empty, only update the name
        $sql = "UPDATE registerusers SET full_name = '$new_name' WHERE id = $user_id";
    }

    if ($conn->query($sql)) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}

// 2. Fetch User Data (Re-fetch to show updated name immediately)
$user_res = $conn->query("SELECT full_name FROM registerusers WHERE id = $user_id");
$user_data = $user_res->fetch_assoc();

// 3. Get Stats (Total Orders & Pending)
$stats_res = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending FROM orders WHERE customer_email = '$user_email'");
$stats = $stats_res->fetch_assoc();

// 4. Recent Orders
$recent_orders = $conn->query("SELECT * FROM orders WHERE customer_email = '$user_email' ORDER BY order_date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --accent: wheat; --dark: #111; --glass: rgba(255, 255, 255, 0.9); }
        
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; height: 100vh; background: #f0f2f5; color: #333; }

        /* --- Glass Sidebar --- */
        .sidebar { width: 280px; background: var(--dark); color: white; padding: 40px 20px; display: flex; flex-direction: column; box-shadow: 4px 0 15px rgba(0,0,0,0.1); }
        .brand { font-size: 1.8rem; font-weight: 900; color: var(--accent); margin-bottom: 50px; text-align: center; letter-spacing: -1px; }
        .sidebar a { text-decoration: none; color: #888; padding: 15px 20px; border-radius: 12px; margin-bottom: 8px; display: flex; align-items: center; gap: 15px; font-weight: 600; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 107, 107, 0.1); color: white; }
        .sidebar a.active { border-left: 5px solid var(--accent); color: var(--accent); }

        /* --- Content Area --- */
        .main { flex: 1; padding: 50px; overflow-y: auto; }
        .welcome-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .btn-shop { background: var(--dark); color: white; padding: 12px 25px; border-radius: 30px; text-decoration: none; font-weight: 700; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .btn-shop:hover { background: var(--accent); transform: translateY(-2px); color: #111; }

        /* --- Hero Stats Grid --- */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 50px; }
        .stat-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); position: relative; overflow: hidden; }
        .stat-card::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background: var(--dark); }
        .stat-card.prime::after { background: var(--accent); }
        .stat-card h4 { margin: 0; font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 1.5px; }
        .stat-card .value { font-size: 2.5rem; font-weight: 900; margin-top: 15px; display: block; }

        /* --- Order Table --- */
        .order-section { background: white; padding: 40px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .section-header { font-size: 1.4rem; font-weight: 800; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 20px; color: #bbb; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
        td { padding: 20px; border-top: 1px solid #f8f8f8; font-weight: 500; }
        
        .badge { padding: 6px 15px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .status-pending { background: #fff4e5; color: #ff9800; }
        .status-shipped { background: #e8f5e9; color: #4caf50; }

        /* --- NEW: Modal Styles --- */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal-box { background: white; padding: 30px; border-radius: 15px; width: 400px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); animation: fadeIn 0.3s ease; }
        .modal-box h2 { margin-top: 0; margin-bottom: 20px; color: var(--dark); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.9rem; color: #666; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-save { width: 100%; background: var(--dark); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-save:hover { background: var(--accent); color: #111; }
        .btn-close { background: transparent; border: none; float: right; font-size: 1.5rem; cursor: pointer; }
        
        .edit-icon { cursor: pointer; color: #999; font-size: 1rem; margin-left: 10px; transition: 0.2s; }
        .edit-icon:hover { color: var(--dark); }
        
        .alert { padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="homeindex.php" ><i class="fas fa-th-home"></i> Home</a>
        <a href="profile.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="catelouge.php"><i class="fas fa-shopping-bag"></i> Shop Now</a>
        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
        <a href="logout.php" style="margin-top: auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <?php if ($message): ?>
            <div class="alert"><?= $message; ?></div>
        <?php endif; ?>

        <div class="welcome-header">
            <div>
                <h1 style="margin:0; font-weight:900; font-size: 2.5rem;">
                    Hi, <?= htmlspecialchars($user_data['full_name']); ?>! 
                    <i class="fas fa-pen edit-icon" onclick="openModal()" title="Edit Profile"></i>
                </h1>
                <p style="color: #888; margin-top: 10px;">Check your orders and manage your premium kicks.</p>
            </div>
            <a href="catelouge.php" class="btn-shop">Explore Collection</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card prime">
                <h4>Total Orders</h4>
                <span class="value"><?= $stats['total'] ?? 0; ?></span>
            </div>
            <div class="stat-card">
                <h4>Pending Delivery</h4>
                <span class="value"><?= $stats['pending'] ?? 0; ?></span>
            </div>
            <div class="stat-card">
                <h4>Wallet Points</h4>
                <span class="value">RM 0.00</span>
            </div>
        </div>

        <div class="order-section">
            <div class="section-header"><i class="fas fa-history" style="color: var(--accent);"></i> Recent Purchases</div>
            <table>
                <thead>
                    <tr>
                        <th>Order Ref</th>
                        <th>Purchase Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_orders->num_rows > 0): ?>
                        <?php while($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 800;">#ORD-<?= $order['order_id'] ?></td>
                            <td style="color: #777;"><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                            <td style="font-weight: 700;">RM <?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge status-<?= strtolower($order['status']) ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 50px; color: #ccc;">
                                <i class="fas fa-box-open fa-3x d-block mb-3"></i>
                                No orders yet. Time to get some shoes!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-box">
            <button class="btn-close" onclick="closeModal()">&times;</button>
            <h2>Edit Profile</h2>
            <form method="POST" action="">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user_data['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>New Password <small>(Leave blank to keep current)</small></label>
                    <input type="password" name="password" placeholder="••••••••">
                </div>

                <button type="submit" class="btn-save">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
