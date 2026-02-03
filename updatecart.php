<?php
session_start();
header('Content-Type: application/json');

if (isset($_GET['index']) && isset($_GET['action'])) {
    $index = $_GET['index'];
    $action = $_GET['action'];
    $value = $_GET['value'];

    if (isset($_SESSION['cart'][$index])) {
        switch ($action) {
            case 'quantity':
                
                $newQty = max(1, intval($value));
                $_SESSION['cart'][$index]['quantity'] = $newQty;
                break;

            case 'color':
                $_SESSION['cart'][$index]['colors'] = htmlspecialchars($value);
                break;

            case 'remove':
                array_splice($_SESSION['cart'], $index, 1);
                break;
        }
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);