<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle DELETE Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: manage_categories.php");
    exit();
}

// 3. Handle POST Request (Add or Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['catName']);
    $icon = mysqli_real_escape_string($conn, $_POST['catIcon']);
    $parent = mysqli_real_escape_string($conn, $_POST['parentCat']);
    $desc = mysqli_real_escape_string($conn, $_POST['catDesc']);
    $catId = isset($_POST['catId']) ? intval($_POST['catId']) : 0;

    if ($catId > 0) {
        // Update Existing
        $sql = "UPDATE categories SET name='$name', icon='$icon', parent_cat='$parent', description='$desc' WHERE id=$catId";
    } else {
        // Insert New
        $sql = "INSERT INTO categories (name, icon, parent_cat, description) VALUES ('$name', '$icon', '$parent', '$desc')";
    }
    
    $conn->query($sql);
    header("Location: manage_categories.php");
    exit();
}


$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Categories | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Keep your existing CSS styles here */
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f1f4f6; }
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 40px; color: wheat; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: block; border-radius: 6px; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid #dde400; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .cat-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        input, select, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .save-btn { width: 100%; padding: 12px; background-color: #dde400; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
        .cat-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #eee; }
        .cat-icon { width: 40px; height: 40px; background-color: #f4f4f4; border-radius: 5px; display: flex; justify-content: center; align-items: center; margin-right: 15px;}
        .tag { background: #eee; padding: 2px 8px; border-radius: 4px; font-size: 0.75em; color: #666; }
        .action-btn { background: none; border: none; cursor: pointer; color: #aaa; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
    <a href="adminmanagecustomer.php" ><i class="fa-solid fa-users"></i> Customers</a>
    <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
    <a href="manage_products.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
    <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
    <a href="adminlogin.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
</div>>
    </div>

    <div class="main-content">
        <h1>Shoe Categories</h1>
        
        <div class="cat-grid">
            <div class="card">
                <h3 id="formTitle">Create Collection</h3>
                <form action="manage_categories.php" method="POST" id="catForm">
                    <input type="hidden" name="catId" id="catId" value="">
                    
                    <label>Category Name</label>
                    <input type="text" name="catName" id="catName" required>

                    <label>Category Icon</label>
                    <select name="catIcon" id="catIcon">
                        <option value="👟">👟 Sneaker</option>
                        <option value="🏃">🏃 Running</option>
                        <option value="🏀">🏀 Basketball</option>
                        <option value="👠">👠 Heels</option>
                        <option value="🔥">🔥 Hot/New</option>
                    </select>

                    <label>Parent Category</label>
                    <select name="parentCat" id="parentCat">
                        <option value="Top Level">None</option>
                        <option value="Men's Shoes">Men's Shoes</option>
                        <option value="Women's Shoes">Women's Shoes</option>
                    </select>

                    <label>Description</label>
                    <textarea name="catDesc" id="catDesc" rows="3"></textarea>

                    <button type="submit" id="submitBtn" class="save-btn">Add Category</button>
                    <button type="button" onclick="resetForm()" id="cancelBtn" style="display:none; width: 100%; margin-top: 10px; border:none; background:#ddd; padding:10px; border-radius:5px;">Cancel</button>
                </form>
            </div>

            <div class="card">
                <h3>Current Inventory Categories</h3>
                <div id="categoryList">
                    <?php while($row = $categories->fetch_assoc()): ?>
                    <div class="cat-item">
                        <div style="display:flex; align-items:center;">
                            <div class="cat-icon"><?php echo $row['icon']; ?></div>
                            <div>
                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                <span class="tag"><?php echo htmlspecialchars($row['parent_cat']); ?></span>
                                <div style="font-size:0.85em; color:#888;"><?php echo htmlspecialchars($row['description']); ?></div>
                            </div>
                        </div>
                        <div>
                            <button class="action-btn" onclick="editCategory(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="manage_categories.php?delete=<?php echo $row['id']; ?>" class="action-btn" onclick="return confirm('Are you sure?')">
                                <i class="fa-solid fa-trash" style="color:#ff6b6b"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Use JSON to pass PHP data to JS for editing
        function editCategory(data) {
            document.getElementById('catId').value = data.id;
            document.getElementById('catName').value = data.name;
            document.getElementById('catIcon').value = data.icon;
            document.getElementById('parentCat').value = data.parent_cat;
            document.getElementById('catDesc').value = data.description;

            document.getElementById('formTitle').innerText = "Edit Category";
            document.getElementById('submitBtn').innerText = "Update Category";
            document.getElementById('cancelBtn').style.display = "block";
        }

        function resetForm() {
            document.getElementById('catId').value = "";
            document.getElementById('catForm').reset();
            document.getElementById('formTitle').innerText = "Create Collection";
            document.getElementById('submitBtn').innerText = "Add Category";
            document.getElementById('cancelBtn').style.display = "none";
        }
    </script>
</body>
</html>