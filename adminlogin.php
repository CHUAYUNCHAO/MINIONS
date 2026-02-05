<?php
session_start();
require_once 'Minionshoesconfig.php'; 

// 1. Generate CSRF Token for Security
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Security Check: Verify CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Validation Failed.");
    }

    $admin_id = trim($_POST['admin_id']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // 3. Database Lookup
    $stmt = $conn->prepare("SELECT admin_id, password, admin_name FROM admins WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        // 4. Verify Password
        if (password_verify($password, $admin['password'])) {
            // SUCCESS: Prevent Session Fixation
            session_regenerate_id(true);
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_id'] = $admin['admin_id'];

            // 5. Handle "Remember Me" Cookie
            if ($remember) {
                setcookie("saved_admin_id", $admin_id, time() + (86400 * 30), "/"); // 30 Days
            } else {
                setcookie("saved_admin_id", "", time() - 3600, "/"); // Clear cookie
            }

            header("Location: adminmanagecustomer.php"); 
            exit();
        } else {
            $error_msg = "Invalid password. Please try again.";
        }
    } else {
        $error_msg = "Admin ID not found in system.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            margin: 0; font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=2070&auto=format&fit=crop') no-repeat center center fixed; 
            background-size: cover; 
            display: flex; align-items: center; justify-content: center; height: 100vh; 
        }
        .login-card { 
            background: rgba(255, 255, 255, 0.98); 
            padding: 40px; border-radius: 16px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.5); 
            width: 100%; max-width: 380px; text-align: center; 
            transform: translateY(0); transition: 0.3s;
        }
        .login-card:hover { transform: translateY(-5px); }
        
        .logo { font-size: 1.8rem; font-weight: 900; color: #111; margin-bottom: 5px; letter-spacing: 1px; }
        .badge { background-color: #ffc107; color: #333; padding: 5px 10px; border-radius: 20px; font-size: 0.7em; text-transform: uppercase; font-weight: 800; display: inline-block; margin-bottom: 25px; letter-spacing: 1px; }
        
        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; color: #555; font-size: 0.9em; font-weight: 700; }
        .input-group i.field-icon { position: absolute; left: 15px; top: 42px; color: #999; }
        
        input[type="text"], input[type="password"] { 
            width: 100%; padding: 12px 15px 12px 40px; 
            border: 2px solid #eee; border-radius: 8px; 
            box-sizing: border-box; font-size: 1rem; transition: 0.3s; 
        }
        input:focus { border-color: #333; outline: none; background: #fff; }
        
        button { 
            width: 100%; padding: 14px; 
            background-color: #111; color: white; 
            border: none; border-radius: 8px; 
            font-weight: bold; font-size: 1rem; cursor: pointer; 
            margin-top: 10px; transition: 0.3s; 
        }
        button:hover { background-color: #ffc107; color: #111; }
        button:disabled { background-color: #999; cursor: wait; }

        .php-error { 
            color: #721c24; background-color: #f8d7da; 
            border: 1px solid #f5c6cb; padding: 12px; 
            border-radius: 8px; font-size: 0.9em; margin-bottom: 20px; 
            animation: shake 0.5s ease-in-out; 
        }
        
        .toggle-password { position: absolute; right: 15px; top: 42px; cursor: pointer; color: #999; transition: 0.3s; }
        .toggle-password:hover { color: #333; }

        .options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 0.85em; color: #666; }
        .options label { display: flex; align-items: center; gap: 5px; cursor: pointer; }
        
        @keyframes shake {
            0% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } 100% { transform: translateX(0); }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo">🍌 MINION SHOE</div>
        <span class="badge">Staff Portal</span>

        <?php if (!empty($error_msg)): ?>
            <div class="php-error">
                <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return showLoading()">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="input-group">
                <label>Admin ID</label>
                <i class="fas fa-user field-icon"></i>
                <input type="text" name="admin_id" placeholder="Enter ID (e.g. 242001)" 
                       value="<?= isset($_COOKIE['saved_admin_id']) ? htmlspecialchars($_COOKIE['saved_admin_id']) : ''; ?>" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <i class="fas fa-lock field-icon"></i>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>

            <div class="options">
                <label>
                    <input type="checkbox" name="remember" <?= isset($_COOKIE['saved_admin_id']) ? 'checked' : ''; ?>> Remember ID
                </label>
                <a href="adminforgotpassword.php" style="color:#666; text-decoration:none;">Forgot Password?</a>
            </div>

            <button type="submit" id="loginBtn">Secure Login</button>
        </form>

        <a href="custloginandregister.php" style="margin-top:25px; display:block; color:#888; font-size:0.85em; text-decoration:none; font-weight:600;">
            ← Back to Customer Store
        </a>
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

        function showLoading() {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Authenticating...';
            btn.disabled = true;
            btn.style.opacity = '0.7';
            return true; // Allow form submission
        }
    </script>
</body>
</html>