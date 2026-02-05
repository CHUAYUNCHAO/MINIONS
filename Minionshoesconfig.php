<?php
// Minionshoesconfig.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "minion_shoe_db";

$conn = new mysqli($host, $user, $pass, database: $db);
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// SECURITY CHECK: Kick out banned users immediately
// This works on every page that requires this config file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    // Check their status in the LIVE database
    $statusCheck = $conn->query("SELECT account_status FROM registerusers WHERE id = $uid");
    
    if ($statusCheck && $statusCheck->num_rows > 0) {
        $userData = $statusCheck->fetch_assoc();
        if ($userData['account_status'] === 'Inactive') {
            // Force Logout
            session_unset();
            session_destroy();
            echo "<script>
                alert('Your account has been deactivated by Admin.'); 
                window.location.href='custloginandregister.php?error=account_suspended';
            </script>";
            exit();
        }
    }
}
?>