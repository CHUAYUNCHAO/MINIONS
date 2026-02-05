<?php
session_start();
require_once('Minionshoesconfig.php'); 

$response = ['success' => false, 'message' => 'Unknown error'];

// 1. SECURITY CHECK: Is user logged in?
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'login_required']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $product_id = intval($_POST['product_id']);
    $quantity   = intval($_POST['quantity']);
    $size       = $_POST['size'] ?? 'Standard';
    
    // Check for color input
    $posted_color = $_POST['color'] ?? ''; 

    if ($product_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM allproducts WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();

            // 1. FETCH COLORS
            $db_colors = $product['colors'] ?? 'Standard'; 
            
            // 2. SET DEFAULT COLOR
            $available_colors_array = explode(',', $db_colors);
            
            // Use posted color or default to first available
            $selected_color = $posted_color ? $posted_color : trim($available_colors_array[0]);

            // --- THE FIX IS HERE ---
            // 3. CREATE CART KEY
            // We must include ID + Size + Color so "Red" and "Blue" are separate items
            $cartKey = $product_id . '-' . $size . '-' . $selected_color;

            if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

            if (isset($_SESSION['cart'][$cartKey])) {
                // If exact same item (same id, size, AND color) exists, update quantity
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            } else {
                // Otherwise add as new item
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
            $response['message'] = 'Added to cart!';
        } else {
            $response['message'] = 'Product not found.';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>