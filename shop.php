<?php
session_start();
require_once('catelogue.php');
$products = $conn->query("SELECT * FROM allproducts ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; padding: 20px; }
        .product-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s; }
        .product-card:hover { transform: translateY(-5px); }
        .img-container { height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .img-container img { max-height: 100%; max-width: 100%; object-fit: contain; }
    </style>
</head>
<body>
    <header style="display: flex; justify-content: space-between; padding: 20px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div style="font-weight: 800; font-size: 1.5rem;">🍌 MINION SHOE</div>
        <nav><a href="profile.php" class="me-3">Dashboard</a><a href="cart.php">🛒 Cart</a></nav>
    </header>

    <div class="container mt-4">
        <input type="text" id="searchInput" class="form-control mb-4" placeholder="Search shoes..." onkeyup="filterItems()">
        <div class="product-grid" id="productGrid">
            <?php while($p = $products->fetch_assoc()): ?>
                <div class="product-card" data-name="<?= strtolower($p['product_name']) ?>">
                    <div class="img-container">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Shoe">
                    </div>
                    <div class="p-3">
                        <small class="text-muted text-uppercase"><?= $p['category'] ?></small>
                        <h5 class="fw-bold"><?= htmlspecialchars($p['product_name']) ?></h5>
                        <p class="text-danger fw-bold">RM <?= number_format($p['price'], 2) ?></p>
                        <button class="btn btn-outline-dark w-100" onclick="addToCart(<?= $p['id'] ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function filterItems() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = card.dataset.name.includes(input) ? "block" : "none";
            });
        }
    </script>
</body>
</html>