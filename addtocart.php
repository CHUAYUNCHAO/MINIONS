<?php
session_start();
require_once('Minionshoesconfig.php'); 

$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $product_id = intval($_POST['product_id']);
    $quantity   = intval($_POST['quantity']);
    $size       = $_POST['size'] ?? 'Standard';
    
    // Check if the user selected a specific color from the modal (optional), otherwise we set a default later
    $posted_color = $_POST['color'] ?? ''; 

    if ($product_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM allproducts WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();

            // 1. FETCH COLORS FROM DATABASE
            // This gets the string like "black,red,white"
            $db_colors = $product['colors'] ?? 'Standard'; 
            
            // 2. SET DEFAULT COLOR
            // Convert "black,red,white" into an array -> ['black', 'red', 'white']
            $available_colors_array = explode(',', $db_colors);
            
            // If user picked a color, use it. Otherwise, pick the first color from the list.
            $selected_color = $posted_color ? $posted_color : trim($available_colors_array[0]);

            // 3. CREATE CART ITEM
            // We use ID-Size as the unique key. 
            $cartKey = $product_id . '-' . $size;

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
                    'selected_color' => $selected_color, // The specific color chosen
                    'all_colors'     => $db_colors       // The list of ALL options for this shoe
                ];
            }

            $response['success'] = true;
            $response['message'] = 'Added to cart!';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>