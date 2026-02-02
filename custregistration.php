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
    $fname     = $_POST['fname'];
    $email     = $_POST['email'];
    $shoe_size = $_POST['shoe_size'];
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    
    $checkEmail = $conn->prepare("SELECT email FROM registerusers WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    if ($checkEmail->get_result()->num_rows > 0) {
        $message = "<div style='color: red; text-align: center;'>Email already registered!</div>";
    } else {
       
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
        button { background: deepskyblue; color: white; font-weight: bold; cursor: pointer; border: none; }
    </style>
</head>
<body>
    <div class="reg-card">
        <h2 style="text-align:center">🍌 Become a Member</h2>
        <?php echo $message; ?>
        <form action="" method="POST">
            <label>Full Name</label>
            <input type="text" name="fname" placeholder="Gru Chua" required>
            <label>Email</label>
            <input type="email" name="email" placeholder="gru@gmail.com"required>
            <label>Password</label>
            <input type="password" name="password" required>
            <label>Shoe Size</label>
            <input type="text" name="size" placeholder="US 10/UK 10" required>
            <button type="submit">CREATE ACCOUNT</button>
        </form>
        <p style="text-align:center">Already a member? <a href="custloginandregister.php">Sign In</a></p>
    </div>
</body>
</html>