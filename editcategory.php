<?php
session_start();

// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Process the Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capture data from the category form
    $id     = intval($_POST['catId']); // Hidden ID field
    $name   = mysqli_real_escape_string($conn, $_POST['catName']);
    $icon   = mysqli_real_escape_string($conn, $_POST['catIcon']);
    $parent = mysqli_real_escape_string($conn, $_POST['parentCat']);
    $desc   = mysqli_real_escape_string($conn, $_POST['catDesc']);

    // 3. Prepare Update Query for 'categories' table
    $stmt = $conn->prepare("UPDATE categories SET name=?, icon=?, parent_cat=?, description=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $icon, $parent, $desc, $id);

    if ($stmt->execute()) {
        // Redirect back to the categories management page with a success status
        header("Location: manage_categories.php?status=updated");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
    
    $stmt->close();
}
$conn->close();
?>