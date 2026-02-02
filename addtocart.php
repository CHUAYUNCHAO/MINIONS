<?php
session_start();
header('Content-Type: application/json');
require_once('Minionshoesconfig.php');

if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);

    // Fetch details from 'catelog' table
    $stmt = $conn->prepare("SELECT * FROM catelog WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image_url'],
                'quantity' => 1,
                'category' => $product['category_group']
            ];
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
}
?>