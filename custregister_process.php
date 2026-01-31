<?php
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname     = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname     = mysqli_real_escape_string($conn, $_POST['lname']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $shoe_size = mysqli_real_escape_string($conn, $_POST['shoe_size']);
    // Securely hash the password
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.history.back();</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, shoe_size) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fname, $lname, $email, $password, $shoe_size);
        
        if ($stmt->execute()) {
            echo "<script>alert('Account created! Please sign in.'); window.location.href='Customerlogin.html';</script>";
        }
    }
}
?>