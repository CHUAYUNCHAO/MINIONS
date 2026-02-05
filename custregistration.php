<?php
session_start();

if(isset($_SESSION['user_id'])) {
    header("Location: homeindex.php");
    exit();
}

$host = "localhost";
$user = "root";      
$pass = "";           
$db   = "minion_shoe_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname     = trim($_POST['fname']);
    $email     = trim($_POST['email']);
    $shoe_size = trim($_POST['shoe_size']); // Note: Your HTML input name was "size" but PHP looked for "shoe_size". I fixed HTML below to match this.
    $password  = $_POST['password'];

    // --- 1. EMAIL DOMAIN VALIDATION START ---
    
    // Get the domain (part after @)
    $email_parts = explode('@', $email);
    $domain = strtolower(end($email_parts)); // Convert to lowercase for comparison

    // List of allowed official providers
    $allowed_providers = [
        'gmail.com', 
        'yahoo.com', 
        'outlook.com', 
        'hotmail.com', 
        'icloud.com'
    ];

    // Check if domain is in allowed list OR ends with .edu (Student emails)
    $is_official = in_array($domain, $allowed_providers) || 
                   str_ends_with($domain, '.edu') || 
                   str_ends_with($domain, '.edu.my');

    if (!$is_official) {
        $message = "<div style='color: red; text-align: center; margin-bottom:10px;'>
                        <strong>Restricted!</strong><br>Please use an official email address<br>(Gmail, Yahoo, or Student Email).
                    </div>";
    } 
    // --- EMAIL DOMAIN VALIDATION END ---
    
    else {
        // Proceed with Database Check
        $checkEmail = $conn->prepare("SELECT email FROM registerusers WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        
        if ($checkEmail->get_result()->num_rows > 0) {
            $message = "<div style='color: red; text-align: center;'>Email already registered!</div>";
        } else {
            // Hash Password & Insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO registerusers (full_name, email, password, shoe_size) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fname, $email, $hashed_password, $shoe_size);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href='custloginandregister.php';</script>";
                exit();
            } else {
                $message = "<div style='color: red;'>Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join Us - Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: url('https://wallpapercave.com/wp/wp9637073.jpg') center/cover; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .reg-card { background: white; padding: 40px; border-radius: 10px; width: 100%; max-width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        input, select, button { width: 100%; padding: 12px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background: deepskyblue; color: white; font-weight: bold; cursor: pointer; border: none; margin-top: 20px; transition: 0.3s; }
        button:hover { background: #0099cc; }
        label { font-weight: 600; font-size: 0.9em; color: #555; margin-top: 10px; display: block; }
    </style>
</head>
<body>
    <div class="reg-card">
        <h2 style="text-align:center">🍌 Become a Member</h2>
        
        <?php echo $message; ?>
        
        <form action="" method="POST">
            <label>Full Name</label>
            <input type="text" name="fname" placeholder="Gru Chua" required>
            
            <label>Official Email</label>
            <input type="email" name="email" placeholder="gru@gmail.com or student@edu.my" required>
            
            <label>Password</label>
            <input type="password" name="password" required>
            
            <label>Shoe Size</label>
            <input type="text" name="shoe_size" placeholder="US 10 / UK 10" required>
            
            <button type="submit">CREATE ACCOUNT</button>
        </form>
        
        <p style="text-align:center; margin-top: 20px; font-size: 0.9em;">
            Already a member? <a href="custloginandregister.php" style="color:deepskyblue; text-decoration:none;">Sign In</a>
        </p>
    </div>
</body>
</html>
