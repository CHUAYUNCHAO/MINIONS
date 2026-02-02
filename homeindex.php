<?php
session_start();
require_once 'Minionshoesconfig.php';
include 'homeheader.php';

// 1. Fetch products from the 'products' table
$sql = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);

// 2. Check for SQL errors
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>
<style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; color: #333; line-height: 1.6; }
        a { text-decoration: none; color: inherit; transition: 0.3s; }
        header { background: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .brand { font-size: 1.8rem; font-weight: 800; letter-spacing: 2px; color: #111; }
        nav { display: flex; gap: 25px; }
        nav a { font-weight: 600; color: #555; font-size: 0.95rem; }
        nav a:hover { color: #ff6b6b; }
        .nav-icons { display: flex; gap: 15px; align-items: center; }
        .btn-login, .btn-admin { background-color: #111; color: white; padding: 8px 20px; border-radius: 20px; font-size: 0.9rem; }
        .btn-login:hover, .btn-admin:hover { background-color: #ff6b6b; }
        .hero { height: 60vh; background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=2012&auto=format&fit=crop'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 3px; }
        .hero p { font-size: 1.2rem; margin-bottom: 30px; font-weight: 300; }
        .btn-cta { padding: 15px 40px; background-color: #ff6b6b; color: white; font-weight: bold; font-size: 1rem; border: none; cursor: pointer; text-transform: uppercase; border-radius: 4px; }
        .container { max-width: 1200px; margin: 50px auto; padding: 0 20px; }
        .section-title { text-align: center; font-size: 2rem; margin-bottom: 40px; color: #111; text-transform: uppercase; letter-spacing: 1px; position: relative; }
        .section-title::after { content: ''; display: block; width: 60px; height: 3px; background: #ff6b6b; margin: 10px auto 0; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease; position: relative; }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .card-image { height: 250px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .card-image img { width: 100%; height: 100%; object-fit: cover; }
        .card-details { padding: 20px; }
        .category { color: #888; font-size: 0.8rem; text-transform: uppercase; font-weight: bold; }
        .card-details h4 { font-size: 1.2rem; margin: 5px 0 10px; color: #111; }
        .price { color: #ff6b6b; font-size: 1.1rem; font-weight: bold; display: block; margin-bottom: 15px; }
        .btn-card { width: 100%; padding: 10px; border: 2px solid #111; background: transparent; color: #111; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-card:hover { background: #111; color: white; }
        footer { background: #1a1a1a; color: #888; text-align: center; padding: 40px 20px; margin-top: 80px; }
    </style>
<div class="hero">
    <h1>Step Into Greatness</h1>
    <p>The latest drops, exclusive styles, and premium comfort.</p>
    <button class="btn-cta" onclick="window.location.href='#trending'">Shop New Arrivals</button>
</div>

<div class="container" id="trending">
    <h2 class="section-title">Trending Now</h2>

    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="card-image">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Shoe">
                    </div>
                    <div class="card-details">
                        <span class="category"><?php echo htmlspecialchars($product['category']); ?></span>
                        <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                        <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                        <a href="product_details.php?id=<?php echo $product['id']; ?>">
                            <button class="btn-card">View Details</button>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; grid-column: 1/-1;">No products found in the inventory.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'homefooter.php'; ?>