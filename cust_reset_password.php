<?php
session_start();
require_once 'Minionshoesconfig.php';

$msg = "";
$msg_type = ""; // To control color (red/green)
$valid_token = false;

// 1. Verify Token from URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $current_time = date("Y-m-d H:i:s");

    // Check if token exists, matches email, and hasn't expired
    $stmt = $conn->prepare("SELECT id FROM registerusers WHERE email = ? AND reset_token = ? AND token_expiry > ?");
    $stmt->bind_param("sss", $email, $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $valid_token = true;
    } else {
        $msg = "Invalid or expired link. Please request a new one.";
        $msg_type = "color: red;";
    }
} else {
    $msg = "No token provided.";
    $msg_type = "color: red;";
}

// 2. Handle Password Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_pass'])) {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $email = $_POST['email'];

    if ($new_pass === $confirm_pass) {
        // Hash the new password
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT); // Default works well for bcrypt

        // Update DB: Set new password and clear the token
        $update = $conn->prepare("UPDATE registerusers SET password = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
        $update->bind_param("ss", $hashed_password, $email);

        if ($update->execute()) {
            $msg = "Password reset successfully! <br><a href='custloginandregister.php'>Login Now</a>";
            $msg_type = "color: green;";
            $valid_token = false; // Hide form after success
        } else {
            $msg = "Database error.";
            $msg_type = "color: red;";
        }
    } else {
        $msg = "Passwords do not match.";
        $msg_type = "color: red;";
        $valid_token = true; // Keep form open to try again
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password - Minion Shoe</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; }
        
        /* Consistent Split Design */
        .split-left { flex: 1; background: #1a1a1a; display: flex; align-items: center; justify-content: center; color: wheat; flex-direction: column; }
        .split-left h1 { font-size: 2.5rem; margin-bottom: 10px; }
        
        .split-right { flex: 1; background: white; display: flex; align-items: center; justify-content: center; }
        .container { width: 100%; max-width: 350px; text-align: center; }
        
        /* Form Elements */
        input, button { width: 100%; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ddd; box-sizing: border-box; }
        button { background: #1a1a1a; color: white; cursor: pointer; font-weight: bold; transition: 0.3s; }
        button:hover { background: #333; }
        
        .message { margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="split-left">
        <h1>Secure Reset</h1>
        <p>Create a new strong password.</p>
    </div>

    <div class="split-right">
        <div class="container">
            
            <?php if ($msg): ?>
                <div class="message" style="<?= $msg_type ?>"><?= $msg ?></div>
            <?php endif; ?>

            <?php if ($valid_token): ?>
                <form method="POST">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email']) ?>">
                    
                    <input type="password" name="new_pass" placeholder="New Password" required minlength="6">
                    <input type="password" name="confirm_pass" placeholder="Confirm Password" required>
                    
                    <button type="submit">Update Password</button>
                </form>
            <?php endif; ?>

            <?php if (!$valid_token && empty($msg)): ?>
               <p>Please use the link sent to your email.</p>
            <?php endif; ?>

            <a href="custloginandregister.php" style="text-decoration:none; color:#666; font-size:0.9rem;">Back to Login</a>
        </div>
    </div>

</body>
</html>