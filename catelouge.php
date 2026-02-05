<?php
session_start();
// Adjust this filename if your config file is named differently (e.g. Minionshoesconfig.php)
require_once('Minionshoesconfig.php');

// Fetch all products
$query = "SELECT * FROM allproducts ORDER BY id DESC";
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
    <title>Shop Collection | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; color: #333; }
        
        /* Header */
        header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .brand { font-size: 1.5rem; font-weight: 800; letter-spacing: 1px; color: #111; }
        nav { display: flex; gap: 20px; }
        nav a { font-weight: 600; color: #555; text-decoration: none; transition: 0.3s; }
        nav a:hover, nav a.active { color: #ff6b6b; }
        
        /* Shop Layout */
        .shop-container { max-width: 1400px; margin: 40px auto; padding: 0 20px; }
        
        /* Sidebar Filter */
        .filter-sidebar { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); height: fit-content; }
        .filter-title { font-weight: 800; font-size: 1.1rem; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .filter-group { margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .filter-group:last-child { border-bottom: none; }
        .filter-group label { display: block; margin-bottom: 10px; cursor: pointer; color: #555; font-weight: 500; }
        .filter-group input[type="checkbox"] { margin-right: 10px; accent-color: #ff6b6b; }
        
        /* Product Grid */
        .product-card { background: white; border-radius: 12px; overflow: hidden; transition: 0.3s; border: 1px solid #eee; position: relative; display: flex; flex-direction: column; height: 100%; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); border-color: #ff6b6b; }
        .card-image { height: 220px; background-color: #f4f4f4; display: flex; align-items: center; justify-content: center; position: relative; }
        .card-image img { max-width: 90%; max-height: 90%; object-fit: contain; transition: 0.3s; }
        .product-card:hover .card-image img { transform: scale(1.05); }
        .badge-cat { position: absolute; top: 10px; left: 10px; background: white; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .card-details { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .product-title { font-weight: 700; font-size: 1rem; margin-bottom: 5px; color: #333; }
        .product-price { color: #ff6b6b; font-weight: 800; font-size: 1.1rem; margin-bottom: 15px; }
        
        .btn-add { width: 100%; padding: 10px; border: 2px solid #111; background: transparent; color: #111; font-weight: 700; border-radius: 8px; transition: 0.3s; margin-top: auto; }
        .btn-add:hover { background: #111; color: white; }

        /* Toast */
        .toast-notification { position: fixed; bottom: 30px; right: 30px; background: #333; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); transform: translateY(100px); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 2000; opacity: 0; }
        .toast-notification.show { transform: translateY(0); opacity: 1; }
    </style>
</head>
<body>

    <header>
        <div class="brand">🍌 MINION SHOE</div>
        <nav class="d-none d-md-flex">
            <a href="homeindex.php">Home</a>
            <a href="catelouge.php" class="active">Shop</a>
            <a href="shoedetail.php">Gallery</a>
            <a href="aboutus.php">About</a>
        </nav>
        <div class="d-flex align-items-center gap-3">
            <a href="cart.php" class="text-dark text-decoration-none fw-bold">
                <i class="fa-solid fa-cart-shopping me-1"></i> Cart
            </a>
        </div>
    </header>

    <div class="shop-container">
        <div class="row g-4">
            
            <div class="col-lg-3">
                <div class="filter-sidebar sticky-top" style="top: 100px; z-index: 90;">
                    <div class="filter-title">
                        Filters <i class="fas fa-filter text-muted"></i>
                    </div>
                    
                    <div class="filter-group">
                        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search shoes..." onkeyup="applyFilters()">
                    </div>

                    <div class="filter-group">
                        <h6 class="fw-bold mb-3">Categories</h6>
                        <label><input type="checkbox" class="cat-filter" value="Men" onchange="applyFilters()"> Men</label>
                        <label><input type="checkbox" class="cat-filter" value="Women" onchange="applyFilters()"> Women</label>
                        <label><input type="checkbox" class="cat-filter" value="Kids" onchange="applyFilters()"> Kids</label>
                    </div>

                    <div class="filter-group">
                        <h6 class="fw-bold mb-3">Sort By</h6>
                        <select id="sortSelect" class="form-select" onchange="applyFilters()">
                            <option value="newest">Newest Arrivals</option>
                            <option value="low-high">Price: Low to High</option>
                            <option value="high-low">Price: High to Low</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-outline-dark w-100 btn-sm" onclick="resetFilters()">Reset Filters</button>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold m-0">All Products</h4>
                    <span class="text-muted small" id="productCount">Showing all items</span>
                </div>

                <div class="row g-4" id="productGrid">
                    </div>
                
                <div id="emptyState" class="text-center py-5 d-none">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No products found.</h4>
                    <p class="text-muted">Try adjusting your filters.</p>
                </div>
            </div>

        </div>
    </div>

    <div id="toast" class="toast-notification">
        <i class="fas fa-check-circle me-2 text-warning"></i> 
        <span id="toastMsg">Added to cart!</span>
    </div>

    <script>
        // 1. Load Data
        const products = <?= json_encode($products); ?>;
        
        // 2. Main Render & Filter Function
        function applyFilters() {
            const container = document.getElementById('productGrid');
            const emptyState = document.getElementById('emptyState');
            const countLabel = document.getElementById('productCount');
            const search = document.getElementById('searchInput').value.toLowerCase();
            const sort = document.getElementById('sortSelect').value;
            
            // Get selected categories
            const checkedCats = Array.from(document.querySelectorAll('.cat-filter:checked')).map(cb => cb.value.toLowerCase());

            container.innerHTML = '';

            // A. Filter Logic (FIXED FOR MEN/WOMEN)
            let filtered = products.filter(p => {
                const matchesSearch = p.product_name.toLowerCase().includes(search);
                
                // If no checkboxes checked, show all categories
                if (checkedCats.length === 0) {
                    return matchesSearch;
                }

                // Check if product matches ANY of the checked categories
                const matchesCat = checkedCats.some(cat => {
                    const prodCat = p.category.toLowerCase();
                    // Fix: If filter is "men", exclude "women"
                    if (cat === 'men') {
                        return prodCat.includes('men') && !prodCat.includes('women');
                    }
                    return prodCat.includes(cat);
                });

                return matchesSearch && matchesCat;
            });

            // B. Sort Logic
            if (sort === 'low-high') {
                filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
            } else if (sort === 'high-low') {
                filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
            } else {
                filtered.sort((a, b) => b.id - a.id); // Default ID sort
            }

            // C. Update UI
            countLabel.innerText = `Showing ${filtered.length} items`;

            if (filtered.length === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
                filtered.forEach(p => {
                    // Image fallback logic
                    let imgSrc = p.image_url ? p.image_url : 'https://via.placeholder.com/300';
                    
                    container.innerHTML += `
                        <div class="col-md-6 col-lg-4">
                            <div class="product-card">
                                <span class="badge-cat">${p.category}</span>
                                <div class="card-image">
                                    <img src="${imgSrc}" alt="${p.product_name}" onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                                </div>
                                <div class="card-details">
                                    <div class="product-title">${p.product_name}</div>
                                    <div class="product-price">RM ${parseFloat(p.price).toFixed(2)}</div>
                                    <button class="btn-add" onclick="addToCart(${p.id}, '${p.product_name.replace(/'/g, "\\'")}')">
                                        Add to Cart <i class="fas fa-plus ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });
            }
        }

        // 3. Add to Cart (Unified & Fixed)
        function addToCart(productId, productName) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            // Default size/color for Quick Add
            formData.append('size', 'Standard');
            formData.append('color', 'Standard');
            
            fetch('addtocart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // LOGIN CHECK
                if(data.message === 'login_required') {
                    if(confirm("Please log in first to add items to your cart.")) {
                        window.location.href = "custloginandregister.php";
                    }
                    return;
                }

                if(data.success) {
                    showToast(`${productName} added to cart!`);
                } else {
                    showToast('Error: ' + data.message, true);
                }
            })
            .catch(error => showToast('Connection error', true));
        }

        // 4. Toast UI
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            toastMsg.innerText = msg;
            toast.style.background = isError ? '#dc3545' : '#1a1a1a';
            
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // 5. Reset
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('sortSelect').value = 'newest';
            document.querySelectorAll('.cat-filter').forEach(cb => cb.checked = false);
            applyFilters();
        }

        // Initial Load
        applyFilters();
    </script>
</body>
</html>