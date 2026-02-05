<?php
session_start();
require_once('Minionshoesconfig.php'); 

// Default response
$response = ['success' => false, 'message' => 'Unknown error'];

// --- 1. SECURITY CHECK: FORCE LOGIN ---
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'login_required']);
    exit(); // Stop execution immediately
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $product_id = intval($_POST['product_id']);
    $quantity   = intval($_POST['quantity']);
    $size       = $_POST['size'] ?? 'Standard';
    
    // Check if user selected a color, otherwise default
    $posted_color = $_POST['color'] ?? ''; 

    if ($product_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM allproducts WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();

            // Handle Colors
            $db_colors = $product['colors'] ?? 'Standard'; 
            $available_colors_array = explode(',', $db_colors);
            $selected_color = $posted_color ? $posted_color : trim($available_colors_array[0]);

            // Create Unique Cart Key
            $cartKey = $product_id . '-' . $size . '-' . $selected_color;

            if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$cartKey] = [
                    'id'             => $product['id'],
                    'name'           => $product['product_name'],
                    'price'          => $product['price'],
                    'image'          => $product['image_url'],
                    'size'           => $size,
                    'quantity'       => $quantity,
                    'selected_color' => $selected_color,
                    'all_colors'     => $db_colors 
                ];
            }

            $response['success'] = true;
            $response['message'] = 'Added to cart successfully!';
        } else {
            $response['message'] = 'Product not found.';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>