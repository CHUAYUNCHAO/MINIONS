<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle DELETE Request
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    // Prepare the SQL command to remove the category
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to the categories page with a deleted status
        header("Location: adminmanagecategories.php?status=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>