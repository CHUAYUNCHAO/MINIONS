<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle DELETE Request (BUG FIXED: Changed 'allproducts' to 'categories')
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: adminmanagecategories.php?msg=deleted");
        exit();
    }
}

// 3. Handle POST Request (Add or Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['catName'];
    $icon = $_POST['catIcon'];
    $parent = $_POST['parentCat'];
    $desc = $_POST['catDesc'];
    $catId = isset($_POST['catId']) ? intval($_POST['catId']) : 0;

    if ($catId > 0) {
        // Update Existing
        $stmt = $conn->prepare("UPDATE categories SET name=?, icon=?, parent_cat=?, description=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $icon, $parent, $desc, $catId);
        $status = "updated";
    } else {
        // Insert New
        $stmt = $conn->prepare("INSERT INTO categories (name, icon, parent_cat, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $icon, $parent, $desc);
        $status = "created";
    }
    
    if ($stmt->execute()) {
        header("Location: adminmanagecategories.php?msg=$status");
        exit();
    }
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
$total_cats = $categories->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Categories | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f4f6f9; color: #333; }
        
        /* Sidebar */
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; flex-shrink: 0; }
        .brand { font-size: 1.5rem; font-weight: 800; text-align: center; margin-bottom: 40px; color: wheat; letter-spacing: 1px; }
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid yellow; }
        
        /* Main Content */
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #111; margin: 0; }
        
        /* Grid Layout */
        .cat-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #eee; height: fit-content; }
        
        /* Form Styling */
        label { font-weight: 600; font-size: 0.9rem; color: #555; display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #ffe600; }
        
        .save-btn { width: 100%; padding: 12px; background-color: #1a1a1a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .save-btn:hover { background-color: #333; }
        .cancel-btn { width: 100%; padding: 12px; background-color: #eee; color: #555; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; display: none; }

        /* List Styling */
        .search-box { margin-bottom: 20px; position: relative; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
        .search-input { padding-left: 40px; margin-bottom: 0; }

        .cat-list { max-height: 600px; overflow-y: auto; padding-right: 5px; }
        .cat-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #f0f0f0; border-radius: 10px; margin-bottom: 10px; background: #fff; transition: 0.2s; }
        .cat-item:hover { border-color: #ffe600; transform: translateX(5px); }
        
        .cat-icon-box { width: 45px; height: 45px; background: #fffde7; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 15px; color: #333; }
        .cat-info h4 { margin: 0 0 5px 0; font-size: 1rem; color: #333; }
        .tag { background: #eee; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; color: #555; font-weight: 700; text-transform: uppercase; }
        
        .action-group { display: flex; gap: 10px; }
        .btn-icon { width: 35px; height: 35px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; }
        .btn-edit { background: #e3f2fd; color: #1976d2; }
        .btn-edit:hover { background: #1976d2; color: white; }
        .btn-del { background: #ffebee; color: #c62828; }
        .btn-del:hover { background: #c62828; color: white; }

        /* Toast */
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }

        @media (max-width: 900px) { .cat-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="admindashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="adminmanagecustomer.php"><i class="fa-solid fa-users"></i> Customers</a>
        <a href="adminmanagecategories.php" class="active"><i class="fa-solid fa-layer-group"></i> Categories</a>
        <a href="adminmanageproduct.php"><i class="fa-solid fa-shoe-prints"></i> Products</a>
        <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="adminlogin.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Category Management</h1>
                <p style="color:#777; margin:5px 0 0;">Organize your shoe inventory efficiently.</p>
            </div>
            <div style="background:white; padding:10px 20px; border-radius:8px; font-weight:bold; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                Total: <span style="color:#e6b800; font-size:1.2rem;"><?php echo $total_cats; ?></span>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-<?php echo $_GET['msg']=='deleted'?'danger':'success'; ?>">
                <i class="fas fa-<?php echo $_GET['msg']=='deleted'?'trash':'check-circle'; ?>"></i>
                Category successfully <?php echo htmlspecialchars($_GET['msg']); ?>!
            </div>
        <?php endif; ?>
        
        <div class="cat-grid">
            
            <div class="card">
                <h3 id="formTitle" style="margin-top:0;">Create Collection</h3>
                <form action="adminmanagecategories.php" method="POST" id="catForm">
                    <input type="hidden" name="catId" id="catId" value="">
                    
                    <label>Category Name</label>
                    <input type="text" name="catName" id="catName" required placeholder="e.g. Running">

                    <label>Icon (Emoji)</label>
                    <div style="display:flex; gap:10px;">
                        <select name="catIcon" id="catIcon" style="flex:1;">
                            <option value="👟">👟 Sneaker</option>
                            <option value="🏃">🏃 Running</option>
                            <option value="🏀">🏀 Basketball</option>
                            <option value="👠">👠 Heels</option>
                            <option value="🔥">🔥 Hot</option>
                            <option value="👶">👶 Kids</option>
                        </select>
                    </div>

                    <label>Parent Category</label>
                    <select name="parentCat" id="parentCat">
                        <option value="Top Level">None (Top Level)</option>
                        <option value="Men">Men's Shoes</option>
                        <option value="Women">Women's Shoes</option>
                        <option value="Kids">Kids' Shoes</option>
                    </select>

                    <label>Description</label>
                    <textarea name="catDesc" id="catDesc" rows="3" placeholder="Brief details..."></textarea>

                    <button type="submit" id="submitBtn" class="save-btn"><i class="fas fa-plus"></i> Add Category</button>
                    <button type="button" onclick="resetForm()" id="cancelBtn" class="cancel-btn">Cancel Edit</button>
                </form>
            </div>

            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h3 style="margin:0;">Inventory Categories</h3>
                </div>
                
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search categories..." onkeyup="filterCats()">
                </div>

                <div class="cat-list" id="catListContainer">
                    <?php if($total_cats > 0): ?>
                        <?php while($row = $categories->fetch_assoc()): ?>
                            <div class="cat-item">
                                <div style="display:flex; align-items:center;">
                                    <div class="cat-icon-box"><?php echo $row['icon']; ?></div>
                                    <div class="cat-info">
                                        <h4 class="cat-name"><?php echo htmlspecialchars($row['name']); ?></h4>
                                        <span class="tag"><?php echo htmlspecialchars($row['parent_cat']); ?></span>
                                        <div style="font-size:0.85em; color:#999; margin-top:3px;"><?php echo htmlspecialchars($row['description']); ?></div>
                                    </div>
                                </div>
                                <div class="action-group">
                                    
                                    <a href="adminmanagecategories.php?delete=<?php echo $row['id']; ?>" class="btn-icon btn-del" onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align:center; padding:30px; color:#999;">No categories found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. Edit Logic
        function editCategory(data) {
            document.getElementById('catForm').action = "adminmanagecategories.php"; // ensure POST url

            // Populate fields
            document.getElementById('catId').value = data.id;
            document.getElementById('catName').value = data.name;
            document.getElementById('catIcon').value = data.icon;
            document.getElementById('parentCat').value = data.parent_cat;
            document.getElementById('catDesc').value = data.description;

            // Update UI
            document.getElementById('formTitle').innerText = "Edit Category";
            document.getElementById('submitBtn').innerHTML = "<i class='fas fa-save'></i> Update Category";
            document.getElementById('cancelBtn').style.display = "block";
            
            // Scroll to form (mobile friendly)
            if(window.innerWidth < 900) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // 2. Reset Logic
        function resetForm() {
            document.getElementById('catId').value = "";
            document.getElementById('catForm').reset();
            
            document.getElementById('formTitle').innerText = "Create Collection";
            document.getElementById('submitBtn').innerHTML = "<i class='fas fa-plus'></i> Add Category";
            document.getElementById('cancelBtn').style.display = "none";
        }

        // 3. Search Logic
        function filterCats() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.cat-item');

            items.forEach(item => {
                const name = item.querySelector('.cat-name').innerText.toLowerCase();
                const tag = item.querySelector('.tag').innerText.toLowerCase();
                
                if (name.includes(input) || tag.includes(input)) {
                    item.style.display = "flex";
                } else {
                    item.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>