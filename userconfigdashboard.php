<?php
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}
?>