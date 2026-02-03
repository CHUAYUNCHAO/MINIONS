<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Fetch Products
$query = "SELECT * FROM allproducts ORDER BY id DESC";
$result = $conn->query($query);
$products = [];
while($row = $result->fetch_assoc()) {
    $row['sizes'] = explode(',', $row['sizes'] ?? '');
    $row['colors'] = explode(',', $row['colors'] ?? '');
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Minion Shoe | Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --dark: #111; --accent: #ff6b6b; --grey: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; background: white; padding-bottom: 50px; }
        
        /* Navigation & Header */
        .custom-header { background: linear-gradient(135deg, #111, #333); color: white; padding: 3rem 2rem; text-align: center; border-radius: 0 0 20px 20px; }
        nav { margin-top: 20px; }
        nav a { color: rgba(255,255,255,0.7); margin: 0 15px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        nav a:hover, nav a.active { color: white; }

        /* Product Cards */
        .shoe-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; height: 100%; display: flex; flex-direction: column; border: 1px solid #eee; }
        .shoe-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .img-wrapper { height: 220px; background: var(--grey); display: flex; align-items: center; justify-content: center; padding: 20px; }
        .img-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; mix-blend-mode: multiply; }
        .shoe-body { padding: 20px; }
        .price-tag { color: var(--accent); font-weight: 800; font-size: 1.2rem; }
        .btn-view { width: 100%; padding: 10px; background: transparent; border: 2px solid var(--dark); font-weight: bold; border-radius: 6px; transition: 0.3s; margin-top: 15px; }
        .btn-view:hover { background: var(--dark); color: white; }

        /* Detail Modal */
        .modal-content { border-radius: 20px; border: none; overflow: hidden; }
        .modal-body { padding: 40px; }
        .modal-img-box { background: var(--grey); border-radius: 15px; padding: 20px; display: flex; align-items: center; justify-content: center; }
        .size-option { padding: 8px 15px; border: 1px solid #ddd; border-radius: 8px; margin: 5px; display: inline-block; font-size: 0.85rem; font-weight: 600; color: #666; }
        .color-option { width: 25px; height: 25px; border-radius: 50%; display: inline-block; margin: 5px; border: 2px solid #eee; }
    </style>
</head>
<body>

    <div class="custom-header">
        <div class="container">
            <h1 style="font-weight: 900;"><i class="fas fa-shoe-prints"></i> Minion Shoe Gallery</h1>
            <p>Explore our latest collection with detailed specifications.</p>
            <nav>
                <a href="homeindex.php">Home</a>
                <a href="catelouge.php">Shop</a>
                <a href="shoedetail.php" class="active">Detail Gallery</a>
                <a href="aboutus.php">About</a>
            </nav>
        </div>
    </div>

    <div class="container mt-5">
        <div class="d-flex justify-content-center gap-2 mb-5">
            <button class="btn btn-dark rounded-pill px-4" onclick="filterProducts('all')">All Kicks</button>
            <button class="btn btn-outline-dark rounded-pill px-4" onclick="filterProducts('men')">Men</button>
            <button class="btn btn-outline-dark rounded-pill px-4" onclick="filterProducts('women')">Women</button>
            <button class="btn btn-outline-dark rounded-pill px-4" onclick="filterProducts('kids')">Kids</button>
        </div>

        <div class="row g-4" id="productContainer">
            </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-4 mb-md-0">
                            <div class="modal-img-box">
                                <img id="modalImg" src="" class="img-fluid" alt="Shoe">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small id="modalCat" class="text-muted text-uppercase fw-bold"></small>
                                    <h2 id="modalName" class="fw-bold mb-2"></h2>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <h3 id="modalPrice" class="price-tag mb-3"></h3>
                            <p id="modalDesc" class="text-muted small mb-4"></p>

                            <h6 class="fw-bold mb-2">Available Sizes</h6>
                            <div id="modalSizes" class="mb-4"></div>

                            <h6 class="fw-bold mb-2">Colors</h6>
                            <div id="modalColors" class="mb-4"></div>

                            <button type="button" class="btn btn-dark w-100 py-3 fw-bold" data-bs-dismiss="modal">
                                Close Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const products = <?= json_encode($products); ?>;
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));

        function filterProducts(type) {
            const container = document.getElementById('productContainer');
            container.innerHTML = '';
            const filtered = type === 'all' ? products : products.filter(p => p.group_name === type);

            filtered.forEach(p => {
                container.innerHTML += `
                    <div class="col-md-6 col-lg-3">
                        <div class="shoe-card">
                            <div class="img-wrapper"><img src="${p.image_url}"></div>
                            <div class="shoe-body">
                                <small class="text-muted fw-bold">${p.category}</small>
                                <h5 class="fw-bold my-1">${p.product_name}</h5>
                                <div class="price-tag">RM ${parseFloat(p.price).toFixed(2)}</div>
                                <button class="btn-view" onclick="openModal(${p.id})">
                                    <i class="far fa-eye me-1"></i> View Details
                                </button>
                            </div>
                        </div>
                    </div>`;
            });
        }

        function openModal(id) {
            const p = products.find(item => item.id == id);
            document.getElementById('modalImg').src = p.image_url;
            document.getElementById('modalName').innerText = p.product_name;
            document.getElementById('modalCat').innerText = p.category;
            document.getElementById('modalPrice').innerText = "RM " + parseFloat(p.price).toFixed(2);
            document.getElementById('modalDesc').innerText = p.description;

            document.getElementById('modalSizes').innerHTML = p.sizes.map(s => 
                `<span class="size-option">${s}</span>`
            ).join('');

            document.getElementById('modalColors').innerHTML = p.colors.map(c => 
                `<span class="color-option" style="background:${c.trim()}"></span>`
            ).join('');

            productModal.show();
        }

        // Initial load
        filterProducts('all');
    </script>
</body>
</html>