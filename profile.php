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
$message = "";
$msg_type = "";

// 2. Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name = $conn->real_escape_string($_POST['full_name']);
    $new_email = $conn->real_escape_string($_POST['email']); // Allow email update
    $new_password = $_POST['password'];

    // Dynamic Query Construction
    $updates = ["full_name = '$new_name'", "email = '$new_email'"];
    
    if (!empty($new_password)) {
        // ideally use password_hash($new_password, PASSWORD_DEFAULT)
        $updates[] = "password = '$new_password'"; 
    }

    $sql = "UPDATE registerusers SET " . implode(', ', $updates) . " WHERE id = $user_id";

    if ($conn->query($sql)) {
        $_SESSION['user_name'] = $new_name; // Update session
        $_SESSION['email'] = $new_email;
        $message = "Profile updated successfully!";
        $msg_type = "success";
    } else {
        $message = "Error updating profile: " . $conn->error;
        $msg_type = "danger";
    }
}

// 3. Fetch User Data
$user_res = $conn->query("SELECT * FROM registerusers WHERE id = $user_id");
$user_data = $user_res->fetch_assoc();

// 4. Get Stats
$stats_res = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending FROM orders WHERE customer_email = '$user_email'");
$stats = $stats_res->fetch_assoc();

// 5. Fetch ALL Orders (for the Orders tab)
$orders_res = $conn->query("SELECT * FROM orders WHERE customer_email = '$user_email' ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --accent: wheat; --dark: #111; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: #333; overflow-x: hidden; }

        /* Sidebar */
        .sidebar { width: 260px; background: var(--dark); min-height: 100vh; position: fixed; padding: 30px 20px; z-index: 100; transition: 0.3s; }
        .brand { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 50px; text-align: center; letter-spacing: 1px; }
        .brand span { color: var(--accent); }
        .nav-link { color: rgba(255,255,255,0.7); padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; font-weight: 600; transition: 0.3s; display: flex; align-items: center; gap: 15px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { border-left: 4px solid var(--accent); }
        .nav-link i { width: 20px; text-align: center; }

        /* Main Content */
        .main-content { margin-left: 260px; padding: 40px; transition: 0.3s; }
        
        /* Header Card */
        .profile-header { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 30px; margin-bottom: 30px; position: relative; overflow: hidden; }
        .avatar-circle { width: 100px; height: 100px; background: var(--dark); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; }
        .profile-info h1 { font-weight: 800; font-size: 2rem; margin: 0; }
        .btn-edit { position: absolute; top: 30px; right: 30px; background: #f8f9fa; border: none; padding: 10px 20px; border-radius: 30px; font-weight: 600; transition: 0.2s; color: #555; }
        .btn-edit:hover { background: var(--dark); color: white; }

        /* Stats Cards */
        .stat-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); border-bottom: 4px solid transparent; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card.blue { border-color: #4facfe; }
        .stat-card.orange { border-color: #ffa502; }
        .stat-card.green { border-color: #2ecc71; }
        .stat-value { font-size: 2rem; font-weight: 800; display: block; margin-top: 10px; }
        .stat-label { color: #888; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }

        /* Tabs & Table */
        .nav-tabs { border-bottom: none; gap: 15px; margin-bottom: 20px; }
        .nav-tabs .nav-link { background: white; border: none; border-radius: 30px; padding: 10px 25px; color: #555; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .nav-tabs .nav-link.active { background: var(--dark); color: white; }
        
        .table-custom { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.02); }
        .table-custom th { background: #fafafa; padding: 20px; font-weight: 700; color: #666; border-bottom: 1px solid #eee; }
        .table-custom td { padding: 20px; vertical-align: middle; border-bottom: 1px solid #f9f9f9; }
        .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-shipped { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #842029; }

        /* Modal */
        .modal-content { border-radius: 20px; border: none; }
        .modal-header { border-bottom: 1px solid #eee; padding: 20px 30px; }
        .modal-body { padding: 30px; }
        .form-control { padding: 12px; border-radius: 10px; border: 1px solid #eee; background: #f9f9f9; }
        .form-control:focus { background: white; box-shadow: none; border-color: var(--dark); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            .sidebar.active { transform: translateX(0); }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION <span>SHOE</span></div>
        <nav class="nav flex-column">
            <a href="homeindex.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
            <a href="#" class="nav-link active"><i class="fas fa-user-circle"></i> My Profile</a>
            <a href="catelouge.php" class="nav-link"><i class="fas fa-shopping-bag"></i> Shop</a>
            <a href="cart.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="logout.php" class="nav-link mt-5 text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="avatar-circle">
                <?= strtoupper(substr($user_data['full_name'], 0, 1)) ?>
            </div>
            <div class="profile-info">
                <small class="text-muted text-uppercase fw-bold">Welcome back,</small>
                <h1><?= htmlspecialchars($user_data['full_name']) ?></h1>
                <p class="text-muted mb-0"><i class="far fa-envelope me-2"></i><?= htmlspecialchars($user_data['email']) ?></p>
            </div>
            <button class="btn-edit" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="fas fa-pen me-2"></i> Edit Profile
            </button>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card blue">
                    <span class="stat-label">Total Orders</span>
                    <span class="stat-value"><?= $stats['total'] ?? 0 ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card orange">
                    <span class="stat-label">Pending Delivery</span>
                    <span class="stat-value"><?= $stats['pending'] ?? 0 ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card green">
                    <span class="stat-label">Loyalty Points</span>
                    <span class="stat-value">0</span> </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">Order History</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">Account Settings</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="orders" role="tabpanel">
                <div class="table-custom">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($orders_res->num_rows > 0): ?>
                                    <?php while($order = $orders_res->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#ORD-<?= str_pad($order['order_id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                        <td>RM <?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge-status status-<?= strtolower($order['status']) ?>">
                                                <?= $order['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-light border" onclick="alert('Order Details feature coming soon!')">View</button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-2x mb-3 d-block"></i> No orders found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                    <h5 class="fw-bold mb-4">Security Settings</h5>
                    <p class="text-muted">Two-factor authentication is currently disabled.</p>
                    <button class="btn btn-outline-dark">Enable 2FA</button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user_data['full_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>