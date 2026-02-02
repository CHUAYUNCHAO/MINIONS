<?php
session_start();
require_once('Minionshoesconfig.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Select based on your registerusers table
    $stmt = $conn->prepare("SELECT id, full_name, email, password FROM registerusers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email']; // Crucial for checkout!
            header("Location: homeindex.php");
            exit();
        }
    }
    header("Location: custloginandregister.php?login_error=1");
    exit();
}
?>