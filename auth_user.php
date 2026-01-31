<?php
session_start();
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['first_name'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php"); // Create this page next!
            exit();
        } else {
            echo "<script>alert('Wrong password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }
}
?>