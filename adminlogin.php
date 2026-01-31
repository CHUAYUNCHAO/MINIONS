<?php
session_start();

// 1. Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

$error_msg = "";

// 2. Handle Login Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = mysqli_real_escape_string($conn, $_POST['admin_id']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, password, admin_name FROM admins WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($password, $admin['password'])) {
            // Success! Set Admin Session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin['admin_name'];
            
            header("Location: manage_users.php"); // Redirect to your customer management
            exit();
        } else {
            $error_msg = "Invalid credentials. Access denied.";
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
        /* Keeping your original sleek styling */
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: url(https://static.vecteezy.com/system/resources/thumbnails/036/725/350/small/ai-generated-shoes-store-advertisment-background-with-copy-space-free-photo.jpg); display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 350px; text-align: center; }
        .logo { font-size: 1.5rem; font-weight: bold; color: black; margin-bottom: 5px; letter-spacing: 2px; }
        .badge { background-color: #dde400; color: black; padding: 4px 8px; border-radius: 4px; font-size: 0.7em; text-transform: uppercase; font-weight: bold; }
        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 5px; color: #777; font-size: 0.9em; font-weight: 500; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #34495e; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .php-error { color: #e74c3c; font-size: 0.85em; margin-bottom: 15px; font-weight: bold; }
        .toggle-password { position: absolute; right: 12px; top: 38px; cursor: pointer; color: #999; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo">🍌 MINION SHOE</div>
        <span class="badge">Staff Access Only</span>

        <h3>Admin Login</h3>

        <?php if($error_msg): ?>
            <div class="php-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form id="adminForm" action="adminmanagecustomer.php" method="POST">
            <div class="input-group">
                <label>Admin ID / Email</label>
                <input type="text" id="admin_id" name="admin_id" placeholder="gru@minionshoe.com" required>
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

        <a href="custloginandregister.php" style="margin-top:20px; display:block; color:#999; font-size:0.8em; text-decoration:none;">← Return to Login Page</a>
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