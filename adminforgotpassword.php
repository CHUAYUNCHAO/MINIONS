<?php
session_start();
require_once 'Minionshoesconfig.php';

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = trim($_POST['admin_id']);

    // 1. Check if Admin Exists
    $stmt = $conn->prepare("SELECT admin_id FROM admins WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. Generate Token
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // 3. Save Token to DB
        $update = $conn->prepare("UPDATE admins SET reset_token = ?, token_expiry = ? WHERE admin_id = ?");
        $update->bind_param("sss", $token, $expiry, $admin_id);
        
        if ($update->execute()) {
            // SIMULATION: In real life, send this link via PHPMailer.
            // For now, we show it so you can test it immediately.
            $resetLink = "adminresetpassword.php?token=" . $token . "&id=" . $admin_id;
            $msg = "Reset Link Generated! <br><a href='$resetLink'><b>Click here to Reset Password</b></a>";
            $msg_type = "success";
        } else {
            $msg = "Database error.";
            $msg_type = "danger";
        }
    } else {
        $msg = "Admin ID not found.";
        $msg_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { width: 100%; max-width: 400px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 15px; }
        .btn-reset { background: #111; color: white; width: 100%; padding: 12px; font-weight: bold; border-radius: 8px; transition: 0.3s; }
        .btn-reset:hover { background: #ffc107; color: #111; }
    </style>
</head>
<body>
    <div class="card p-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold">Recovery</h3>
            <p class="text-muted small">Enter your Admin ID to reset access.</p>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?= $msg_type ?> text-center"><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Admin ID</label>
                <input type="text" name="admin_id" class="form-control" required placeholder="e.g. 242001">
            </div>
            <button type="submit" class="btn btn-reset">Generate Reset Link</button>
        </form>
        <div class="text-center mt-3">
            <a href="adminlogin.php" class="text-decoration-none text-muted small">Back to Login</a>
        </div>
    </div>
</body>
</html>