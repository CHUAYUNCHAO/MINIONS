<?php
session_start();

// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $category = $_POST['category'];
    $sku      = $_POST['sku'];
    $price    = $_POST['price'];
    $stock    = $_POST['stock'];
    $colors   = $_POST['colors']; // Ensure your DB table has this column

    // --- IMAGE HANDLING LOGIC ---
    $final_image = 'https://via.placeholder.com/150'; // Default fallback
    $image_mode = $_POST['image_mode']; // 'url' or 'upload'

    if ($image_mode === 'url' && !empty($_POST['image_url'])) {
        $final_image = $_POST['image_url'];
    } elseif ($image_mode === 'upload' && isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $target_dir = "uploads/"; 
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        // Generate unique name to prevent overwriting
        $fileExtension = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid('prod_', true) . '.' . $fileExtension;
        $target_file = $target_dir . $newFileName;
        
        // Allow certain file formats
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowed)) {
            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
                $final_image = $target_file;
            }
        }
    }

    // Prepare SQL (Added 'colors' column)
    // Make sure your database table 'allproducts' has the 'colors' column!
    $stmt = $conn->prepare("INSERT INTO allproducts (product_name, price, stock, category, image_url, sku, colors) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdissss", $name, $price, $stock, $category, $final_image, $sku, $colors);
    
    if ($stmt->execute()) {
        header("Location: adminmanageproduct.php?success=1");
        exit();
    } else {
        $error = "Database Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product | Minion Shoe Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #111; --accent: wheat; --text: #333; --bg: #f4f7f6; --error: #d32f2f; }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg); display: flex; margin: 0; min-height: 100vh; color: var(--text); }
        
        /* Modern Sidebar */
        .sidebar { width: 260px; background-color: var(--primary); color: #fff; padding: 25px; display: flex; flex-direction: column; height: 100vh; position: fixed; box-shadow: 4px 0 15px rgba(0,0,0,0.1); z-index: 100; }
        .brand { color: var(--accent); font-weight: 800; font-size: 1.6rem; text-align: center; margin-bottom: 40px; letter-spacing: 1px; }
        .sidebar a { text-decoration: none; color: #aaa; padding: 14px 18px; display: flex; align-items: center; gap: 12px; border-radius: 8px; transition: 0.3s; margin-bottom: 8px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); color: white; transform: translateX(5px); }
        .sidebar a.active { border-left: 4px solid var(--accent); }

        /* Main Content */
        .main-content { flex: 1; padding: 40px; margin-left: 260px; width: calc(100% - 260px); }
        
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-box h1 { font-size: 2rem; color: var(--primary); margin: 0; font-weight: 800; }
        .back-btn { text-decoration: none; color: #666; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.2s; background: white; padding: 10px 20px; border-radius: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .back-btn:hover { color: var(--primary); transform: translateY(-2px); box-shadow: 0 5px 10px rgba(0,0,0,0.1); }

        /* Form Card */
        .form-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); max-width: 800px; margin: 0 auto; animation: slideUp 0.4s ease-out; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 20px; }
        .full-width { grid-column: span 2; }
        
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; font-size: 0.9rem; letter-spacing: 0.5px; }
        input, select { width: 100%; padding: 14px 15px; border: 2px solid #f0f0f0; border-radius: 10px; font-size: 1rem; transition: 0.3s; box-sizing: border-box; background: #fafafa; }
        input:focus, select:focus { border-color: var(--primary); background: white; outline: none; }
        input::placeholder { color: #ccc; }

        /* Image Tabs */
        .img-section { background: #f9f9f9; padding: 20px; border-radius: 12px; border: 1px solid #eee; }
        .img-tabs { display: flex; margin-bottom: 15px; background: #e0e0e0; border-radius: 8px; padding: 4px; width: fit-content; }
        .tab-btn { padding: 8px 20px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; font-weight: 600; color: #666; transition: 0.3s; }
        .tab-btn.active { background: white; color: var(--primary); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .img-input-group { display: none; animation: fadeIn 0.3s ease; }
        .img-input-group.active { display: block; }
        
        /* File Upload Styling */
        .file-upload-box { border: 2px dashed #ccc; padding: 30px; text-align: center; border-radius: 10px; cursor: pointer; transition: 0.3s; background: white; position: relative; }
        .file-upload-box:hover { border-color: var(--primary); background: #fffbe6; }
        .file-upload-box i { font-size: 2rem; color: #aaa; margin-bottom: 10px; display: block; }
        
        /* Submit Button */
        .btn-save { background-color: var(--primary); color: white; border: none; padding: 18px; width: 100%; border-radius: 10px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: 0.3s; margin-top: 20px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-save:hover { background-color: #333; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        .error-msg { background:#ffebee; color:#c62828; padding:15px; border-radius:8px; margin-bottom:20px; border-left:4px solid #c62828; display: none; }

        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="admindashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <a href="adminmanageproduct.php" class="active"><i class="fa-solid fa-shoe-prints"></i> Products</a>
        <a href="adminmanagecustomer.php"><i class="fa-solid fa-users"></i> Customers</a>
        <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="custloginandregister.php" style="margin-top:auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-box">
            <h1>Add New Product</h1>
            <a href="adminmanageproduct.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Inventory</a>
        </div>

        <div class="form-card">
            <div id="jsError" class="error-msg"></div>
            <?php if(isset($error)): ?>
                <div class="error-msg" style="display:block;"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form id="productForm" action="" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="full-width">
                        <label>Product Name</label>
                        <input type="text" name="name" placeholder="e.g. Nike Air Jordan 1 High" required>
                    </div>

                    <div>
                        <label>Category</label>
                        <select name="category">
                            <option value="Men's Running">Men's Running</option>
                            <option value="Women's Sport">Women's Sport</option>
                            <option value="Kids">Kids</option>
                            <option value="Casual">Casual</option>
                            <option value="Formal">Formal</option>
                        </select>
                    </div>

                    <div>
                        <label>SKU (Stock Keeping Unit)</label>
                        <input type="text" name="sku" placeholder="e.g. NK-2024-001" required>
                    </div>

                    <div>
                        <label>Price (RM)</label>
                        <input type="number" id="price" step="0.01" name="price" placeholder="0.00" required>
                    </div>

                    <div>
                        <label>Initial Stock</label>
                        <input type="number" id="stock" name="stock" placeholder="0" required>
                    </div>

                    <div class="full-width">
                        <label>Available Colors</label>
                        <input type="text" name="colors" placeholder="e.g. Black, Red, White (Comma separated)" required>
                    </div>

                    <div class="full-width img-section">
                        <label style="margin-bottom:15px;">Product Image Source</label>
                        
                        <div class="img-tabs">
                            <div class="tab-btn active" onclick="switchTab('url')"><i class="fas fa-link me-2"></i> Image URL</div>
                           
                        </div>
                        
                        <input type="hidden" name="image_mode" id="imageMode" value="url">

                        <div id="urlInput" class="img-input-group active">
                            <input type="text" name="image_url" id="imgUrlField" placeholder="https://example.com/shoe-image.jpg">
                            <div style="margin-top:10px; font-size:0.85rem; color:#666;">Paste a direct link to an image from the web.</div>
                        </div>

                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-plus-circle"></i> Save Product to Inventory
                </button>
            </form>
        </div>
    </div>

    <script>
        // 1. Tab Switching Logic
        function switchTab(mode) {
            document.getElementById('imageMode').value = mode;
            
            // UI Toggle
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Input Toggle
            if (mode === 'url') {
                document.getElementById('urlInput').classList.add('active');
                document.getElementById('uploadInput').classList.remove('active');
            } else {
                document.getElementById('urlInput').classList.remove('active');
                document.getElementById('uploadInput').classList.add('active');
            }
        }

        // 2. File Preview Text
        function previewFile(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fileNameDisplay').innerHTML = '<i class="fas fa-check-circle"></i> Selected: ' + input.files[0].name;
            }
        }

        // 3. Form Validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            let messages = [];
            let isValid = true;
            
            const price = parseFloat(document.getElementById('price').value);
            const stock = parseInt(document.getElementById('stock').value);
            const mode = document.getElementById('imageMode').value;
            const urlVal = document.getElementById('imgUrlField').value;
            const fileVal = document.getElementById('fileInput').value;

            if (price <= 0) messages.push("Price must be greater than 0.");
            if (stock < 0) messages.push("Stock cannot be negative.");
            
            // Validate Image selection based on mode
            if (mode === 'url' && urlVal.trim() === '') {
                messages.push("Please enter an Image URL.");
                isValid = false;
            } 

            if (messages.length > 0) {
                e.preventDefault();
                const errDiv = document.getElementById('jsError');
                errDiv.innerHTML = messages.join("<br>");
                errDiv.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    </script>

</body>
</html>