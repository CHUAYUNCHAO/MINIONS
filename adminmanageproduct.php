<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle DELETE Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM allproducts WHERE id = $id");
    header("Location: adminmanageproduct.php?status=deleted");
    exit();
}

// 3. Fetch All Products
$products = $conn->query("SELECT * FROM allproducts ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Inventory | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        
        /* Sidebar (FIXED) */
        .sidebar { 
            width: 260px; 
            background-color: #1a1a1a; 
            color: #fff; 
            min-height: 100vh; 
            position: fixed; 
            padding: 20px; 
            z-index: 100;
            /* These lines push the Logout button to the bottom */
            display: flex;
            flex-direction: column; 
        }
        
        .brand { font-size: 1.5rem; font-weight: 800; text-align: center; margin-bottom: 40px; color: wheat; letter-spacing: 1px; }
        
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid yellow; }
        
        /* Main Content */
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        
        /* Toolbar */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .toolbar { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; gap: 20px; margin-bottom: 30px; align-items: center; }
        .search-wrapper { flex-grow: 1; position: relative; }
        .search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; }
        .search-box { width: 100%; padding: 12px 12px 12px 45px; border: 1px solid #eee; border-radius: 8px; background: #f9f9f9; transition: 0.3s; }
        .search-box:focus { background: white; border-color: #ffe600; outline: none; }
        
        .btn-add { background-color: wheat; color: black; padding: 12px 25px; border-radius: 8px; font-weight: 700; border: none; transition: 0.3s; box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3); }
        .btn-add:hover { background-color: yellow; transform: translateY(-2px); }

        /* Table */
        .table-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden; }
        .table th { background: #f8f9fa; border-bottom: 2px solid #eee; color: #555; font-weight: 700; padding: 15px; cursor: pointer; }
        .table th:hover { background: #eee; }
        .table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; }
        
        /* Product Visuals */
        .product-flex { display: flex; align-items: center; gap: 15px; }
        .product-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; background: #f0f0f0; border: 1px solid #eee; }
        .product-name { font-weight: 700; color: #333; display: block; }
        .product-sku { font-size: 0.8rem; color: #888; }
        
        /* Stock & Price */
        .stock-wrapper { width: 120px; }
        .stock-bar { height: 6px; background: #eee; border-radius: 3px; margin-top: 5px; overflow: hidden; }
        .stock-fill { height: 100%; border-radius: 3px; }
        .badge-stock { font-size: 0.75rem; font-weight: 800; padding: 4px 8px; border-radius: 20px; }
        .price-tag { font-weight: 700; color: #333; }

        /* Actions */
        .action-btn { width: 35px; height: 35px; border-radius: 50%; border: none; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; margin-right: 5px; }
        .btn-edit { background: #e3f2fd; color: #3498db; }
        .btn-edit:hover { background: #3498db; color: white; }
        .btn-del { background: #ffebee; color: #e74c3c; }
        .btn-del:hover { background: #e74c3c; color: white; }

        /* Toast */
        .toast-notification { position: fixed; bottom: 30px; right: 30px; background: #333; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); transform: translateY(100px); transition: 0.3s; opacity: 0; z-index: 1050; }
        .toast-notification.show { transform: translateY(0); opacity: 1; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <div>
            <a href="admindashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="adminmanagecustomer.php"><i class="fa-solid fa-users"></i> Customers</a>
            <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
            <a href="adminmanageproduct.php" class="active"><i class="fa-solid fa-shoe-prints"></i> Products</a>
            <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        </div>
        
        <a href="adminlogin.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2 class="fw-bold m-0">Inventory Management</h2>
                <p class="text-muted m-0">Manage your catalog, prices, and stock.</p>
            </div>
            <button class="btn-add" onclick="location.href='addproduct.php'">
                <i class="fas fa-plus me-2"></i> Add New Shoe
            </button>
        </div>

        <div class="toolbar">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="search-box" placeholder="Search by product name, category, or SKU..." onkeyup="filterTable()">
            </div>
            <div style="min-width: 200px;">
                <select class="form-select border-0 bg-light fw-bold text-muted" onchange="filterCategory(this.value)">
                    <option value="All">All Categories</option>
                    <option value="Men">Men's Shoes</option>
                    <option value="Women">Women's Shoes</option>
                    <option value="Kids">Kids' Shoes</option>
                </select>
            </div>
        </div>

        <div class="table-card">
            <table class="table table-hover m-0" id="productTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Product Details <i class="fas fa-sort ms-1 small"></i></th>
                        <th onclick="sortTable(1)">Category <i class="fas fa-sort ms-1 small"></i></th>
                        <th onclick="sortTable(2)">Price <i class="fas fa-sort ms-1 small"></i></th>
                        <th onclick="sortTable(3)">Stock Level <i class="fas fa-sort ms-1 small"></i></th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $products->fetch_assoc()): 
                        $stock = $row['stock'] ?? 0;
                        $stockPercent = min($stock, 100); 
                        
                        // Color Logic
                        if($stock == 0) { $color = '#dc3545'; $badge = 'Out of Stock'; $bg = '#f8d7da'; }
                        elseif($stock < 10) { $color = '#ffc107'; $badge = 'Low Stock'; $bg = '#fff3cd'; }
                        else { $color = '#198754'; $badge = 'In Stock'; $bg = '#d1e7dd'; }
                    ?>
                    <tr>
                        <td>
                            <div class="product-flex">
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="product-img" alt="Shoe">
                                <div>
                                    <span class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></span>
                                    <span class="product-sku">#ID-<?php echo $row['id']; ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="cat-cell"><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><span class="price-tag">RM <?php echo number_format($row['price'], 2); ?></span></td>
                        <td>
                            <div class="stock-wrapper">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge-stock" style="background:<?php echo $bg; ?>; color:<?php echo $color; ?>"><?php echo $badge; ?></span>
                                    <small class="fw-bold"><?php echo $stock; ?></small>
                                </div>
                                <div class="stock-bar">
                                    <div class="stock-fill" style="width: <?php echo $stockPercent; ?>%; background: <?php echo $color; ?>;"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="action-btn btn-edit" title="Edit" onclick="location.href='editproduct.php?id=<?php echo $row['id']; ?>'">
                                <i class="fas fa-pen"></i>
                            </button>
                            <a href="adminmanageproduct.php?delete=<?php echo $row['id']; ?>" 
                               class="action-btn btn-del" 
                               title="Delete"
                               onclick="return confirm('Permanently delete this product?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="toast" class="toast-notification">
        <i class="fas fa-check-circle me-2"></i> Action completed successfully!
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 1. Live Search
        function filterTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#productTable tbody tr");
            
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }

        // 2. Category Filter
        function filterCategory(cat) {
            let rows = document.querySelectorAll("#productTable tbody tr");
            
            rows.forEach(row => {
                let catCell = row.querySelector(".cat-cell").innerText;
                if (cat === "All" || catCell.includes(cat)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        // 3. Table Sorting
        function sortTable(n) {
            let table = document.getElementById("productTable");
            let rows = Array.from(table.rows).slice(1);
            let asc = table.getAttribute("data-order") !== "asc";
            
            rows.sort((a, b) => {
                let x = a.cells[n].innerText.toLowerCase();
                let y = b.cells[n].innerText.toLowerCase();
                
                let xNum = parseFloat(x.replace(/[^0-9.-]+/g,""));
                let yNum = parseFloat(y.replace(/[^0-9.-]+/g,""));
                
                if (!isNaN(xNum) && !isNaN(yNum)) {
                    return asc ? xNum - yNum : yNum - xNum;
                }
                return asc ? x.localeCompare(y) : y.localeCompare(x);
            });

            rows.forEach(row => table.querySelector("tbody").appendChild(row));
            table.setAttribute("data-order", asc ? "asc" : "desc");
        }

        // 4. Toast Logic
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('status')) {
            const toast = document.getElementById('toast');
            setTimeout(() => toast.classList.add('show'), 500);
            setTimeout(() => toast.classList.remove('show'), 3500);
        }
    </script>
</body>
</html>