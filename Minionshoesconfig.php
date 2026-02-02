<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "minion_shoe_db";

// Ensure the 4th parameter is your database name
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>