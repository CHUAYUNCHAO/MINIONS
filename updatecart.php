<?php
session_start();
$response = ['success' => false];

if (isset($_GET['index']) && isset($_GET['action'])) {
    $index = $_GET['index'];
    $action = $_GET['action'];
    $value = $_GET['value'];

    if (isset($_SESSION['cart'][$index])) {
        
        if ($action === 'quantity') {
            $newQty = intval($value);
            if ($newQty > 0) {
                $_SESSION['cart'][$index]['quantity'] = $newQty;
                $response['success'] = true;
            }
        } 
        elseif ($action === 'color') {
            // Save the user's choice to 'selected_color'
            $_SESSION['cart'][$index]['selected_color'] = $value; 
            $response['success'] = true;
        }
        elseif ($action === 'remove') {
            unset($_SESSION['cart'][$index]);
            $response['success'] = true;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>