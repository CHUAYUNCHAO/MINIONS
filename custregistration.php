<?php
$host = "localhost";
$user = "root";      
$pass = "";          
$db   = "minion_shoe_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname     = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname     = mysqli_real_escape_string($conn, $_POST['lname']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $shoe_size = mysqli_real_escape_string($conn, $_POST['shoe_size']);
    
    
    $plain_password = $_POST['password'];
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

   
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        $message = "<div style='color: red; text-align: center;'>Email already registered!</div>";
    } else {
        // Insert into Database
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, shoe_size) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fname, $lname, $email, $hashed_password, $shoe_size);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
        } else {
            $message = "<div style='color: red;'>Error: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
    $checkEmail->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Us Now!!! - Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing CSS stays exactly the same */
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: url('https://wallpapercave.com/wp/wp9637073.jpg'); background-position: center; background-size: cover; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .reg-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        .logo { text-align: center; font-weight: bold; font-size: 1.5rem; letter-spacing: 2px; margin-bottom: 20px; color: #1a1a1a; }
        h2 { text-align: center; margin-bottom: 10px; color: #333; }
        p.subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 0.9em; }
        .form-row { display: flex; gap: 15px; }
        .form-group { flex: 1; margin-bottom: 20px; position: relative; }
        label { display: block; margin-bottom: 5px; color: #444; font-weight: 500; font-size: 0.9em; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; transition: 0.3s; }
        input:focus, select:focus { border-color: #ff6b6b; outline: none; }
        input.error { border-color: red; background-color: #fff0f0; }
        .error-msg { color: red; font-size: 0.75em; margin-top: 5px; display: none; position: absolute; }
        #password { padding-right: 40px; } 
        .toggle-password { position: absolute; right: 15px; top: 38px; cursor: pointer; color: #aaa; z-index: 2; }
        button { width: 100%; padding: 15px; background-color: deepskyblue; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 10px; transition: 0.3s; }
        button:hover { background-color: rgb(0, 132, 168); color: #1a1a1a; }
        .signin-link { text-align: center; margin-top: 20px; font-size: 0.9em; }
        .signin-link a { color: black; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="reg-card">
        <div class="logo">🍌 MINION SHOE</div>
        <h2>Become a Member</h2>
        <p class="subtitle">Get first access to the latest drops and exclusive discounts.</p>

        <?php echo $message; ?>

        <form id="regForm" action="register.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" id="fname" name="fname" placeholder="Gru" required>
                    <div class="error-msg">First name is required</div>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" id="lname" name="lname" placeholder="Kevin" required>
                    <div class="error-msg">Last name is required</div>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" name="email" placeholder="minion@example.com" required>
                <div class="error-msg">Please enter a valid email</div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye"></i>
                </span>
                <div class="error-msg">Password must be at least 6 characters</div>
            </div>

            <div class="form-group">
                <label>My Shoe Size (Optional)</label>
                <select name="shoe_size">
                    <option value="">Select your size...</option>
                    <option value="US 7">US 7</option>
                    <option value="US 8">US 8</option>
                    <option value="US 9">US 9</option>
                    <option value="US 10">US 10</option>
                    <option value="US 11">US 11</option>
                    <option value="US 12">US 12</option>
                </select>
                <small style="color:#888; display:block; margin-top:4px;">We use this to show you what's in stock.</small>
            </div>

            <button type="submit">CREATE ACCOUNT</button>
        </form>

        <div class="signin-link">
            Already a member? <a href="custloginandregister.php">Sign In</a>
        </div>
    </div>

    <script>
        // Password toggle and Validation JS remains same as your original
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