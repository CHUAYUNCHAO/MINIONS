<?php
session_start();
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Fetch Products
$query = "SELECT * FROM productsdetail ORDER BY id DESC";
$result = $conn->query($query);
$products = [];
while($row = $result->fetch_assoc()) {
    // Convert comma-separated strings back into arrays for JavaScript
    $row['sizes'] = explode(',', $row['sizes']);
    $row['colors'] = explode(',', $row['colors']);
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #111; --accent-color: #ff6b6b; --success-color: #2ecc71; }
        body { font-family: 'Segoe UI', sans-serif; background: white; padding-bottom: 50px; }
        h1{ font-weight: bold;}
        .custom-header { background: linear-gradient(135deg, #111, #333); color: white; padding: 3rem 2rem; text-align: center; border-radius: 0 0 20px 20px; }

        nav{ margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        nav a { Color: white; margin: 15px; text-decoration: none; font-weight: bold;} 

        /* Shoe Card Styling */
        .shoe-card { background: white; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; height: 100%; display: flex; flex-direction: column; }
        .shoe-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
        .img-wrapper { height: 220px; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .shoe-card img { max-width: 100%; max-height: 100%; object-fit: contain; transition: 0.5s; }
        
        /* Modal Options Styling */
        .size-option { cursor: pointer; padding: 8px 12px; border: 2px solid #ddd; border-radius: 5px; margin: 5px; display: inline-block; font-weight: 600; }
        .size-option.active { background: #111; color: white; border-color: #111; }
        .color-option { cursor: pointer; width: 30px; height: 30px; border-radius: 50%; display: inline-block; margin: 5px; border: 2px solid #eee; transition: 0.2s; }
        .color-option.active { transform: scale(1.2); border-color: #111; }

        .toast-custom { position: fixed; bottom: 20px; right: 20px; background: var(--success-color); color: white; padding: 15px 25px; border-radius: 10px; transform: translateY(100px); transition: 0.3s; z-index: 1050; }
        .toast-custom.show { transform: translateY(0); }
    </style>
</head>
<body>

    <div class="custom-header">
        <div class="container">
            <h1><i class="fas fa-shoe-prints"></i> Minion Shoe Gallery</h1>
            <p>Select your style and explore the details.</p>
        </div>
        <nav>
            <a href="homeindex.php">Home</a>
            <a href="catelouge.php" class="active">Shop</a>
            <a href="shoedetail.php">Detail</a>
            <a href="aboutus.php">About</a>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="d-flex justify-content-center gap-3 mb-5">
            <button class="btn btn-outline-dark rounded-pill px-4" onclick="filterProducts('all')">All</button>
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
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">View Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5">
                            <img id="modalImg" src="" class="img-fluid rounded shadow-sm" alt="Shoe">
                        </div>
                        <div class="col-md-7">
                            <span id="modalCat" class="text-muted text-uppercase fw-bold small">Category</span>
                            <h2 id="modalName" class="fw-bold mb-2">Name</h2>
                            <h3 id="modalPrice" class="text-danger fw-bold mb-3">RM 0.00</h3>
                            <p id="modalDesc" class="text-muted mb-4">Description goes here...</p>

                            <label class="fw-bold d-block mb-2">Size</label>
                            <div id="modalSizes" class="mb-3"></div>

                            <label class="fw-bold d-block mb-2">Color</label>
                            <div id="modalColors" class="mb-4"></div>

                            <button class="btn btn-dark w-100 py-3 fw-bold" onclick="addToCart()">
                                <i class="fas fa-cart-plus me-2"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="successToast" class="toast-custom">
        <i class="fas fa-check-circle me-2"></i> Item added to cart!
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
                                <h5 class="shoe-title">${p.product_name}</h5>
                                <div class="price-tag">RM ${parseFloat(p.price).toFixed(2)}</div>
                                <button class="btn-view" onclick="openModal(${p.id})">View Details</button>
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

            // Sizes
            let sHtml = '';
            p.sizes.forEach(s => sHtml += `<span class="size-option" onclick="select(this)">${s}</span>`);
            document.getElementById('modalSizes').innerHTML = sHtml;

            // Colors
            let cHtml = '';
            p.colors.forEach(c => cHtml += `<span class="color-option" style="background:${c.trim()}" onclick="select(this)"></span>`);
            document.getElementById('modalColors').innerHTML = cHtml;

            productModal.show();
        }

        function select(el) {
            const siblings = el.parentElement.children;
            for(let s of siblings) s.classList.remove('active');
            el.classList.add('active');
        }

        function addToCart() {
            const s = document.querySelector('.size-option.active');
            const c = document.querySelector('.color-option.active');

            if(!s || !c) return alert("Please pick size & color!");

            productModal.hide();
            const t = document.getElementById('successToast');
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        filterProducts('all');
    </script>
</body>
</html>