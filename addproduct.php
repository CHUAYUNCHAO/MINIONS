<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle the Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $category = $_POST['category'];
    $sku      = $_POST['sku'];
    $price    = $_POST['price'];
    $stock    = $_POST['stock'];
    $image_url = $_POST['image_url']; // Fixed variable name

    // SQL to insert the new shoe
    // Fixed bind_param: "sdis" (string, double, integer, string, string) matches the 5 variables
    $stmt = $conn->prepare("INSERT INTO allproducts (product_name, price, stock, category, image_url, sku) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Assuming you have an 'sku' column. If not, remove the 'sku' from query and types.
    // Types: s=string, d=double(price), i=int(stock), s=string, s=string, s=string
    $stmt->bind_param("sdisss", $name, $price, $stock, $category, $image_url, $sku);
    
    if ($stmt->execute()) {
        // Redirect on success
        header("Location: adminmanageproduct.php?success=1");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f1f4f6; display: flex; margin: 0; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; padding: 20px; display: flex; flex-direction: column; height: 100vh; position: fixed; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px; display: block; border-radius: 6px; transition: 0.3s; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid yellow; }
        .brand { color: wheat; font-weight: bold; font-size: 1.5rem; text-align: center; margin-bottom: 30px; }

        /* Main Content */
        .main-content { flex: 1; padding: 40px; margin-left: 260px; }
        
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
        
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input, select { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; transition: 0.3s; }
        input:focus, select:focus { border-color: #ff6b6b; outline: none; }
        
        .btn-save { background-color: #ff6b6b; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background-color: #e05e5e; }

        /* Image Preview Styling */
        .preview-container { text-align: center; margin-bottom: 20px; display: none; }
        .preview-container img { max-width: 200px; border-radius: 10px; border: 2px solid #eee; padding: 5px; }

        /* Error Message */
        .error-msg { color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 20px; display: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
         <a href="adminDashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
         <a href="adminmanageproduct.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
    </div>

    <div class="main-content">
        <h1 style="text-align: center; margin-bottom: 30px;">Add New Shoe</h1>
        
        <div class="form-card">
            <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <div id="jsError" class="error-msg"></div>

            <form id="productForm" action="addproduct.php" method="POST">
                
                <label>Product Name</label>
                <input type="text" id="name" name="name" placeholder="e.g. Nike Air Max" required>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label>Category</label>
                        <select name="category">
                            <option value="Men's Running">Men's Running</option>
                            <option value="Women's Sport">Women's Sport</option>
                            <option value="Kids">Kids</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label>SKU</label>
                        <input type="text" name="sku" placeholder="e.g. NK-270-RED" required>
                    </div>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label>Price (RM)</label>
                        <input type="number" id="price" step="0.01" name="price" placeholder="0.00" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Initial Stock</label>
                        <input type="number" id="stock" name="stock" value="0" required>
                    </div>
                </div>

                <label>Image URL</label>
                <input type="text" id="imageUrl" name="image_url" placeholder="https://example.com/shoe.jpg">
                
                <div class="preview-container" id="previewContainer">
                    <label style="font-weight: normal; font-size: 0.9em; color: #888;">Preview:</label>
                    <img id="imagePreview" src="" alt="Shoe Preview">
                </div>

                <button type="submit" class="btn-save">Save Product to Inventory</button>
            </form>
        </div>
    </div>

    <script>
        // 1. Image Preview Logic
        const imageInput = document.getElementById('imageUrl');
        const previewContainer = document.getElementById('previewContainer');
        const previewImg = document.getElementById('imagePreview');

        imageInput.addEventListener('input', function() {
            const url = this.value;
            if (url) {
                previewImg.src = url;
                previewContainer.style.display = 'block';
                
                // If image fails to load (broken link), hide preview or show error placeholder
                previewImg.onerror = function() {
                    previewContainer.style.display = 'none';
                };
            } else {
                previewContainer.style.display = 'none';
            }
        });

        // 2. Form Validation Logic
        const form = document.getElementById('productForm');
        const errorDiv = document.getElementById('jsError');

        form.addEventListener('submit', function(e) {
            let isValid = true;
            let messages = [];

            const price = parseFloat(document.getElementById('price').value);
            const stock = parseInt(document.getElementById('stock').value);

            // Validate Price
            if (price <= 0) {
                messages.push("Price must be greater than RM 0.");
                isValid = false;
            }

            // Validate Stock
            if (stock < 0) {
                messages.push("Stock cannot be negative.");
                isValid = false;
            }

            // If invalid, stop submission and show errors
            if (!isValid) {
                e.preventDefault(); // Stop PHP form submission
                errorDiv.innerHTML = messages.join("<br>");
                errorDiv.style.display = 'block';
                // Scroll to top of form to see error
                errorDiv.scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>

</body>
</html>