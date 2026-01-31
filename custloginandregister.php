<?php
session_start();
// If user is already logged in, send them to the dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: .php");
    exit();
}

$error = "";
if (isset($_GET['login_error'])) {
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ... Your existing CSS styles ... */
        body { margin: 0; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; }
        .split-left { flex: 1; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('https://w0.peakpx.com/wallpaper/178/512/HD-wallpaper-sneakers-hype-jordan-klekt-nike-shoes-stock-x-we-the-new.jpg'); background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; color: wheat; flex-direction: column; }
        .split-left h1 { font-size: 3rem; margin: 0; text-transform: uppercase; letter-spacing: 5px; }
        .split-right { flex: 1; background: white; display: flex; align-items: center; justify-content: center; padding: 40px; }
        .login-container { width: 100%; max-width: 400px; }
        .input-group { position: relative; margin-bottom: 20px; }
        input { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; background-color: #f9f9f9; transition: 0.3s; }
        #password { padding-right: 45px; }
        .toggle-password { position: absolute; right: 15px; top: 38px; cursor: pointer; color: #aaa; z-index: 2; }
        button { width: 100%; padding: 15px; background-color: #1a1a1a; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
        .php-error { color: #ff6b6b; margin-bottom: 15px; text-align: center; font-weight: bold; }
        @media (max-width: 768px) { .split-left { display: none; } }
    </style>
</head>
<body>

    <div class="split-left">
        <h1>Minion Shoe</h1>
        <p>STEP INTO GREATNESS</p>
    </div>

    <div class="split-right">
        <div class="login-container">
            <h2>Welcome Back !</h2>
            
            <?php if($error): ?>
                <div class="php-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form id="loginForm" action="auth_user.php" method="POST">
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" id="email" name="email" placeholder="minions@example.com" required>
                </div>
                
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>
                
                <button type="submit">SIGN IN</button>
            </form>
            
            <div class="links">
                  <p>Are you Admin or Staff? <a href="adminlogin.php">Click me</a></p>
                <p>Not a member? <a href="custregistration.php">Join Us</a></p>
                
            </div>
        </div>
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