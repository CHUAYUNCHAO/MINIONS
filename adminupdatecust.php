<?php
session_start();
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id        = intval($_POST['user_id']); // Retrieves the hidden ID
    $fullName  = mysqli_real_escape_string($conn, $_POST['custName']);
    $email     = mysqli_real_escape_string($conn, $_POST['custEmail']);
    $shoeSize  = mysqli_real_escape_string($conn, $_POST['custSize']);
    $spent     = mysqli_real_escape_string($conn, $_POST['custSpent']);
    $status    = mysqli_real_escape_string($conn, $_POST['custStatus']);
    
    // SQL query matching your 'addusers' table structure
    $stmt = $conn->prepare("UPDATE addusers SET full_name=?, email=?, shoe_size=?, total_spent=?, status=? WHERE id=?");
    $stmt->bind_param("sssssi", $fullName, $email, $shoeSize, $spent, $status, $id);

    if ($stmt->execute()) {
        header("Location: adminmanagecustomer.php?status=updated");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>