<?php
session_start();

// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Sanitize and prepare data
    $fullName  = mysqli_real_escape_string($conn, $_POST['custName']);
    $email     = mysqli_real_escape_string($conn, $_POST['custEmail']);
    $shoeSize  = mysqli_real_escape_string($conn, $_POST['custSize']);
    
    // Split Full Name into First and Last
    $nameParts = explode(" ", $fullName, 2);
    $firstName = $nameParts[0];
    $lastName  = isset($nameParts[1]) ? $nameParts[1] : '';

    // Set a default password for admin-created accounts (User can change it later)
    $defaultPass = password_hash("Minion123!", PASSWORD_DEFAULT);

    // 4. Check if email already exists
    $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkQuery->bind_param("s", $email);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Error: This email is already registered.'); window.location.href='adminmanagecustomer.php';</script>";
    } else {
        // 5. Insert into Database
        $stmt = $conn->prepare("INSERT INTO addusers (full_name, email, shoe_size, total_spent, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $fullName, $email, $shoeSize, $spent, $status, $id);

        if ($stmt->execute()) {
            // Success redirect
            header("Location: adminmanagecustomer.php?status=success");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $checkQuery->close();
}
$conn->close();
?>