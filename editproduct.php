<?php
session_start();
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");

// 1. Fetch current data to fill the form
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM allproducts WHERE id = $id");
    $product = $res->fetch_assoc();
}

// 2. Process the Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("UPDATE allproducts SET product_name=?, price=?, stock=?, category=? WHERE id=?");
    $stmt->bind_param("sdisi", $name, $price, $stock, $category, $id);
    
    if ($stmt->execute()) {
        header("Location: adminmanageproduct.php?status=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Minion Shoe</title>
    <style>
        body { font-family: sans-serif; background: #f1f4f6; padding: 40px; }
        .card { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-save { background: #ff6b6b; color: white; border: none; padding: 12px; width: 100%; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Edit Shoe Details</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            
            <label>Shoe Name</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

            <label>Price (RM)</label>
            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>

            <label>Stock Quantity</label>
            <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>

            <label>Category</label>
            <select name="category">
                <option <?php if($product['category'] == "Men's Running") echo 'selected'; ?>>Men's Running</option>
                <option <?php if($product['category'] == "Women's Sport") echo 'selected'; ?>>Women's Sport</option>
                <option <?php if($product['category'] == "Kids") echo 'selected'; ?>>Kids</option>
            </select>

            <button type="submit" class="btn-save">Update Changes</button>
            <a href="adminmanageproduct.php" style="display:block; text-align:center; margin-top:15px; color:#666;">Cancel</a>
        </form>
    </div>
</body>
</html>