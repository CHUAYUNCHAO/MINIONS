<?php
session_start();
header('Content-Type: application/json');

// 1. Validate that the required parameters are sent
if (isset($_GET['index']) && isset($_GET['action'])) {
    $index = intval($_GET['index']);
    $action = $_GET['action'];
    $value = isset($_GET['value']) ? $_GET['value'] : null;

    // 2. Check if the item actually exists in the cart
    if (isset($_SESSION['cart'][$index])) {
        
        if ($action === 'quantity') {
            // BUG FIX: Ensure the value is treated as an integer
            $_SESSION['cart'][$index]['quantity'] = intval($value);
            
        } elseif ($action === 'remove') {
            // BUG FIX: Use array_splice to re-index the array after removal
            array_splice($_SESSION['cart'], $index, 1);
        }
        
        echo json_encode(['success' => true]);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Request']);
?>