<?php
session_start();
require_once 'Minionshoesconfig.php';

$msg = "";
$msg_type = "";
$valid_token = false;

// 1. Verify Token from URL
if (isset($_GET['token']) && isset($_GET['id'])) {
    $token = $_GET['token'];
    $id = $_GET['id'];
    $now = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ? AND reset_token = ? AND token_expiry > ?");
    $stmt->bind_param("sss", $id, $token, $now);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $valid_token = true;
    } else {
        $msg = "Invalid or expired token.";
        $msg_type = "danger";
    }
}

// 2. Handle Password Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_pass'])) {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $id = $_POST['admin_id'];

    if ($new_pass === $confirm_pass) {
        // Hash and Update
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $update = $conn->prepare("UPDATE admins SET password = ?, reset_token = NULL, token_expiry = NULL WHERE admin_id = ?");
        $update->bind_param("ss", $hashed, $id);
        
        if ($update->execute()) {
            $msg = "Password Reset Successfully! <a href='adminlogin.php'>Login Here</a>";
            $msg_type = "success";
            $valid_token = false; // Hide form
        }
    } else {
        $msg = "Passwords do not match.";
        $msg_type = "danger";
        $valid_token = true; // Keep form open
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { width: 100%; max-width: 400px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 15px; }
    </style>
</head>
<body>
    <div class="card p-4">
        <h3 class="fw-bold text-center mb-4">Reset Password</h3>

        <?php if ($msg): ?>
            <div class="alert alert-<?= $msg_type ?> text-center"><?= $msg ?></div>
        <?php endif; ?>

        <?php if ($valid_token): ?>
        <form method="POST">
            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($_GET['id']) ?>">
            
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_pass" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_pass" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-dark w-100">Update Password</button>
        </form>
        <?php elseif(empty($msg)): ?>
            <div class="alert alert-warning">No token provided.</div>
        <?php endif; ?>
    </div>
</body>
</html>