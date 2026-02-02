<?php
session_start();
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    // SQL command to remove the row
    $stmt = $conn->prepare("DELETE FROM addusers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to the list after deleting
        header("Location: adminmanagecustomer.php?status=deleted");
        exit();
    }
}
?>