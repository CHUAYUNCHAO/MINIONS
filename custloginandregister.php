<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: homeindex.php");
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
    <title>Login - Minion Shoe</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex;}
        .split-left { flex: 1; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('https://w0.peakpx.com/wallpaper/178/512/HD-wallpaper-sneakers-hype-jordan-klekt-nike-shoes-stock-x-we-the-new.jpg') center/cover; display: flex; align-items: center; justify-content: center; color: white; flex-direction: column; }
        .split-left h1 { color: wheat; font-weight: bold; font-size: 3rem; margin-bottom: 20px; }
        .split-left p { font-weight: bold; font-size: 1.5rem; }
        .split-right { flex: 1; background: white; display: flex; align-items: center; justify-content: center; }
        .login-container { width: 100%; max-width: 350px; }
        input, button { width: 100%; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ddd; }
        button { background: #1a1a1a; color: white; cursor: pointer; }
        .error { color: red; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="split-left"><h1>Minion Shoe</h1><p>STEP INTO GREATNESS</p></div>
    <div class="split-right">
        <div class="login-container">
            <h2>Welcome Back!</h2>
            <?php if($error) echo "<p class='error'>$error</p>"; ?>
            <form action="auth_user.php" method="POST">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">SIGN IN</button>
                <a href="cust_forgot_password.php" class="forgot-link">Forgot Password?</a>
            </form>
            
            <p>Are you staff or Admin?<a href="adminlogin.php">Click Me</a></p>
            <p>Not a member? <a href="custregistration.php">Join Us</a></p>
        </div>
    </div>
</body>
</html>