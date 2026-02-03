<?php
session_start();
header('Content-Type: application/json');

// Check if all necessary parameters are provided
if (isset($_GET['index']) && isset($_GET['action'])) 
    $index = intval($_GET['index']);
    $action = $_GET['action'];
    $value = isset($_GET['value']) ? $_GET['value'] : null;

    // Check if the item exists in the user's cart session
    if (isset($_SESSION['cart'][$index])) {
        
        if ($action === 'quantity') {
            // Bug Fix: Update the specific item's quantity
            $_SESSION['cart'][$index]['quantity'] = intval($value);
            
        }   if (isset($_SESSION['cart'][$index])) {
        // Handle Color Update
        if ($action === 'color') {
            $_SESSION['cart'][$index]['color'] = htmlspecialchars($value);
            echo json_encode(['success' => true]);
            
        } elseif ($action === 'remove') {
            // Bug Fix: Cleanly remove the item from the session array
            array_splice($_SESSION['cart'], $index, 1);
        }
        
        echo json_encode(['success' => true]);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>