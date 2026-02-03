<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle DELETE Request - FIX: Redirect to THIS file, not categories
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM allproducts WHERE id = $id");
    header("Location: adminmanageproduct.php?status=deleted"); // Fixed path
    exit();
}

// 3. Fetch All Products
$products = $conn->query("SELECT * FROM allproducts ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Products | Minion Shoe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Keep your existing CSS styles here */
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; height: 100vh; background-color: #f1f4f6; }
        .sidebar { width: 260px; background-color: #1a1a1a; color: #fff; display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 40px; letter-spacing: 1px; color: wheat; } 
        .sidebar a { text-decoration: none; color: #b3b3b3; padding: 12px 15px; margin-bottom: 8px; display: block; border-radius: 6px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #333; color: white; border-left: 4px solid yellow; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .toolbar { display: flex; gap: 15px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .search-box { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; color: white; }
        .btn-add { background-color: #ff6b6b; }
        .table-container { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background-color: #f8f9fa; color: #666; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; }
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-img { width: 50px; height: 50px; border-radius: 4px; object-fit: cover; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75em; font-weight: bold; }
        .in-stock { background-color: #d4edda; color: #155724; }
        .low-stock { background-color: #fff3cd; color: #856404; }
        .out-stock { background-color: #f8d7da; color: #721c24; }
        .action-btn { background: none; border: none; cursor: pointer; font-size: 1.1em; margin: 0 5px; color: #888; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">🍌 MINION SHOE</div>
        <a href="adminDashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <a href="adminmanagecustomer.php"><i class="fa-solid fa-users"></i> Customers</a>
        <a href="adminmanagecategories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
        <a href="adminmanageproduct.php" class="active"><i class="fa-solid fa-shoe-prints"></i> Products</a>
        <a href="adminorders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="adminlogin.php" style="margin-top: auto;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Product Inventory</h1>
            <button class="btn btn-add" onclick="location.href='addproduct.php'">+ Add New Shoe</button>
        </div>

        <?php if(isset($_GET['status'])): ?>
            <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:20px; border-radius:5px;">
                Product successfully <?php echo htmlspecialchars($_GET['status']); ?>!
            </div>
        <?php endif; ?>

        <div class="toolbar">
            <input type="text" class="search-box" placeholder="Search shoes..." id="searchInput" onkeyup="filterTable()">
        </div>

        <div class="table-container">
            <table id="productTable">
                <thead>
                    <tr>
                        <th width="35%">Product Details</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $products->fetch_assoc()): 
                        $stock = $row['stock'] ?? 0;
                        $badgeClass = ($stock > 10) ? 'in-stock' : (($stock > 0) ? 'low-stock' : 'out-stock');
                    ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                
                                <div>
                                    <span class="product-name"><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></span>
                                    <span class="product-cat"><?php echo htmlspecialchars($row['category']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="price">RM <?php echo number_format($row['price'], 2); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $stock; ?> Units</span></td>
                        <td>
                            <button class="action-btn" onclick="location.href='editproduct.php?id=<?php echo $row['id']; ?>'">
                                <i class="fa-solid fa-pen" style="color:#3498db"></i>
                            </button>
                            <a href="adminmanageproduct.php?delete=<?php echo $row['id']; ?>" 
                               class="action-btn" 
                               onclick="return confirm('Permanently delete this shoe?')">
                                <i class="fa-solid fa-trash" style="color:#ff6b6b"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>