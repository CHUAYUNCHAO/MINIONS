<?php
session_start();
require_once 'Minionshoesconfig.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST['admin_id'];
    $password = $_POST['password']; // This is the plain text 'Abc123' from the form

    // 1. Fetch the hashed password and name from the DB
    $stmt = $conn->prepare("SELECT admin_id, password, admin_name FROM admins WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        // 2. Use password_verify to compare the form input to the DB hash
        if (password_verify($password, $admin['password'])) {
            // SUCCESS
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin['admin_name'];
            header("Location: adminmanagecustomer.php"); 
            exit();
        } else {
            $error_msg = "Invalid password. Access denied.";
        }
    } else {
        $error_msg = "Admin ID not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: url('https://static.vecteezy.com/system/resources/thumbnails/036/725/350/small/ai-generated-shoes-store-advertisment-background-with-copy-space-free-photo.jpg') no-repeat center center fixed; background-size: cover; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); width: 100%; max-width: 360px; text-align: center; }
        .logo { font-size: 1.6rem; font-weight: bold; color: #111; margin-bottom: 5px; letter-spacing: 2px; }
        .badge { background-color: #f1c40f; color: black; padding: 4px 8px; border-radius: 4px; font-size: 0.75em; text-transform: uppercase; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 8px; color: #555; font-size: 0.9em; font-weight: 600; }
        input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 13px; background-color: #2c3e50; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.3s; }
        button:hover { background-color: #1a252f; }
        .php-error { color: #e74c3c; font-size: 0.85em; margin-bottom: 15px; font-weight: bold; background: #fdeaea; padding: 10px; border-radius: 5px; }
        .toggle-password { position: absolute; right: 12px; top: 40px; cursor: pointer; color: #999; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo">🍌 MINION SHOE</div>
        <span class="badge">Staff Access Only</span>

        <?php if (!empty($error_msg)): ?>
    <div class="php-error">
        <i class="fas fa-circle-exclamation me-1"></i> 
        <?= htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label>Admin ID</label>
                <input type="text" name="admin_id" placeholder="242XX24XXX" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>

            <button type="submit">Access Dashboard</button>
        </form>

        <a href="custloginandregister.php" style="margin-top:25px; display:block; color:#777; font-size:0.85em; text-decoration:none;">← Return to Login Page</a>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = "password";
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>