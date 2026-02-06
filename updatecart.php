<?php
session_start();

$response = ['success' => false];

if (isset($_GET['index']) && isset($_GET['action']) && isset($_GET['value'])) {
    $index = $_GET['index'];
    $action = $_GET['action'];
    $value = $_GET['value'];

    // Check if the item exists in the cart
    if (isset($_SESSION['cart'][$index])) {
        
        switch ($action) {
            case 'remove':
                unset($_SESSION['cart'][$index]);
                break;

            case 'quantity':
                // Ensure quantity is at least 1
                if ($value > 0) {
                    $_SESSION['cart'][$index]['quantity'] = intval($value);
                }
                break;

            case 'color':
                // Update selected color
                $_SESSION['cart'][$index]['selected_color'] = $value;
                break;

            case 'size':
                // Update selected size
                $_SESSION['cart'][$index]['size'] = $value;
                break;
        }

        $response['success'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>