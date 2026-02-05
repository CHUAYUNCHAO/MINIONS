<?php
session_start();
require_once 'Minionshoesconfig.php';

$message = "";
$msg_style = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // 1. Check if email exists
    $stmt = $conn->prepare("SELECT id FROM registerusers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. Generate Token
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // 3. Save to DB
        $update = $conn->prepare("UPDATE registerusers SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // 4. Show Simulation Link (In real app, email this)
        $link = "cust_reset_password.php?token=$token&email=$email";
        $message = "Reset link generated! <a href='$link'>Click here to reset</a>";
        $msg_style = "color: green;";
    } else {
        $message = "Email not found.";
        $msg_style = "color: red;";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Minion Shoe</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; }
        .split-left { flex: 1; background: #1a1a1a; display: flex; align-items: center; justify-content: center; color: wheat; flex-direction: column; }
        .split-right { flex: 1; background: white; display: flex; align-items: center; justify-content: center; }
        .container { width: 100%; max-width: 350px; }
        input, button { width: 100%; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ddd; box-sizing: border-box; }
        button { background: #1a1a1a; color: white; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="split-left"><h1>Recovery</h1></div>
    <div class="split-right">
        <div class="container">
            <h2>Reset Password</h2>
            <p style="color:#666; margin-bottom:20px;">Enter your email to receive a reset link.</p>
            
            <?php if($message) echo "<p style='font-weight:bold; $msg_style'>$message</p>"; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            
            <a href="custloginandregister.php" style="text-decoration:none; color:#1a1a1a;">← Back to Login</a>
        </div>
    </div>
</body>
</html>