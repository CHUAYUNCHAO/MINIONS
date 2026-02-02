<?php
session_start();
require_once('Minionshoesconfig.php');

// Fetch all products from 'catelog' table
$query = "SELECT * FROM catelog ORDER BY id DESC";
$result = $conn->query($query);
$products = [];
while($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --accent-color: #ff6b6b; --primary-color: #111; }
        header { background: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; padding: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; transition: 0.3s; border: 1px solid #f0f0f0; }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .card-image { height: 250px; background-color: #f8f8f8; display: flex; align-items: center; justify-content: center; }
        .card-image img { max-width: 100%; max-height: 100%; object-fit: contain; mix-blend-mode: multiply; }
        .btn-add { width: 100%; padding: 10px; border: 2px solid var(--primary-color); background: transparent; font-weight: 700; transition: 0.3s; }
        .btn-add:hover { background: var(--primary-color); color: white; }
        nav a { color: black; text-decoration: none; margin: 15px; font-weight: bold;}
    </style>
</head>
<body>
    <header>
        <div class="brand">🍌 MINION SHOE</div>
        <nav>
          <a href="homeindex.php">Home</a>
            <a href="catelouge.php" class="active">Shop</a>
            <a href="shoedetail.php">Detail</a>
            <a href="aboutus.php">About</a>
    </nav>
        </nav>
        <div class="nav-icons">
            <a href="cart.php" style="text-decoration:none; color:inherit;"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
        </div>
    </header>

    <div class="container mt-5">
        <div class="product-grid" id="productGrid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Shoe">
                    </div>
                    <div class="p-3">
                        <small class="text-muted text-uppercase fw-bold"><?= htmlspecialchars($p['category_group']) ?></small>
                        <h4 class="h6 fw-bold"><?= htmlspecialchars($p['name']) ?></h4>
                        <p class="text-danger fw-bold">RM <?= number_format($p['price'], 2) ?></p>
                        <button class="btn-add" onclick="addToCart(<?= $p['id'] ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function addToCart(productId) {
            // Updated to match your filename
            fetch('addtocart.php?id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert('Shoe added to your cart!');
                    }
                });
        }
    </script>
</body>
</html>