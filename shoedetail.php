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
    $row['sizes'] = !empty($row['sizes']) ? explode(',', $row['sizes']) : ['7','8','9','10','11']; 
    $row['colors'] = !empty($row['colors']) ? explode(',', $row['colors']) : ['#000','#fff']; 
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
        body { font-family: 'Segoe UI', sans-serif; background: #fafafa; padding-bottom: 50px; }
        
        /* Header */
        .custom-header { background: #111; color: white; padding: 4rem 2rem; text-align: center; margin-bottom: 30px; }
        .custom-header h1 { font-weight: 900; letter-spacing: -1px; }
        .nav-links a { color: #888; text-decoration: none; margin: 0 15px; font-weight: 600; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { color: var(--accent); }

        /* Toolbar */
        .toolbar { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); margin-bottom: 40px; }
        .filter-btn { border: none; background: none; font-weight: 700; color: #555; padding: 8px 15px; border-radius: 20px; transition: 0.3s; }
        .filter-btn:hover, .filter-btn.active { background: var(--dark); color: white; }
        .search-input { border-radius: 20px; border: 1px solid #eee; background: #f9f9f9; padding: 10px 20px; }

        /* Product Cards */
        .shoe-card { background: white; border-radius: 15px; overflow: hidden; transition: 0.3s; border: 1px solid #eee; position: relative; }
        .shoe-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-color: var(--accent); }
        .img-wrapper { height: 250px; background: #f4f4f4; display: flex; align-items: center; justify-content: center; padding: 20px; position: relative; }
        .img-wrapper img { max-width: 90%; max-height: 90%; object-fit: contain; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1)); transition: 0.3s; }
        .shoe-card:hover .img-wrapper img { transform: scale(1.05); }
        
        .badge-cat { position: absolute; top: 15px; left: 15px; background: white; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .shoe-body { padding: 20px; }
        .shoe-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .price-tag { color: var(--accent); font-weight: 800; font-size: 1.2rem; }
        
        .btn-view { width: 100%; background: var(--dark); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; margin-top: 15px; transition: 0.3s; }
        .btn-view:hover { background: var(--accent); }

        /* Modal */
        .modal-content { border-radius: 20px; overflow: hidden; border: none; }
        .modal-img-container { background: #f4f4f4; display: flex; align-items: center; justify-content: center; height: 100%; min-height: 300px; }
        .size-radio { display: none; }
        .size-label { cursor: pointer; display: inline-block; width: 40px; height: 40px; line-height: 38px; text-align: center; border: 1px solid #ddd; border-radius: 8px; margin-right: 5px; font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
        .size-radio:checked + .size-label { background: var(--dark); color: white; border-color: var(--dark); }
    </style>
</head>
<body>

    <div class="custom-header">
        <div class="container">
            <h1>👟 MINION SHOE GALLERY</h1>
            <p class="text-white-50">Premium kicks for premium minions.</p>
            <div class="nav-links mt-4">
                <a href="homeindex.php">Home</a>
                <a href="catelouge.php">Shop</a>
                <a href="catelouge.php" class="active">Gallery</a>
                <a href="cart.php">My Cart</a>
                <a href="aboutus.php">About</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="toolbar">
            <div class="row align-items-center g-3">
                <div class="col-md-6 d-flex gap-2 flex-wrap">
                    <button class="filter-btn active" onclick="setCategory('all', this)">All</button>
                    <button class="filter-btn" onclick="setCategory('Men', this)">Men</button>
                    <button class="filter-btn" onclick="setCategory('Women', this)">Women</button>
                    <button class="filter-btn" onclick="setCategory('Kids', this)">Kids</button>
                </div>
                <div class="col-md-3">
                    <select class="form-select border-0 bg-light rounded-pill" onchange="setSort(this.value)">
                        <option value="newest">Sort by: Newest</option>
                        <option value="low-high">Price: Low to High</option>
                        <option value="high-low">Price: High to Low</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control search-input" placeholder="Search shoes..." onkeyup="setSearch(this.value)">
                </div>
            </div>
        </div>

        <div class="row g-4" id="productContainer"></div>
        
        <div id="emptyState" class="text-center py-5 d-none">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No shoes found matching your criteria.</h4>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal"></button>
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="modal-img-container">
                                <img id="modalImg" src="" class="img-fluid" style="max-height: 300px;" alt="Shoe">
                            </div>
                        </div>
                        <div class="col-lg-6 p-5">
                            <div id="addToCartSection">
                                <input type="hidden" id="modalId">
                                
                                <span id="modalCat" class="badge bg-warning text-dark mb-2"></span>
                                <h2 id="modalName" class="fw-bold mb-2"></h2>
                                <h3 id="modalPrice" class="price-tag mb-3"></h3>
                                <p id="modalDesc" class="text-muted mb-4 small"></p>

                                <div class="mb-4">
                                    <label class="fw-bold d-block mb-2">Select Size</label>
                                    <div id="modalSizes"></div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-4">
                                        <input type="number" id="modalQty" class="form-control text-center py-3 rounded-3 fw-bold" value="1" min="1" max="10">
                                    </div>
                                    <div class="col-8">
                                        <button type="button" onclick="triggerAddToCart()" class="btn btn-dark w-100 py-3 rounded-3 fw-bold">
                                            Add to Cart <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 1. Data Initialization
    const products = <?= json_encode($products); ?>;
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));
    
    let state = { category: 'all', search: '', sort: 'newest' };

    // 2. Render Function (THE FIX IS HERE)
    function render() {
        const container = document.getElementById('productContainer');
        const emptyState = document.getElementById('emptyState');
        container.innerHTML = '';

        let filtered = products.filter(p => {
            // --- 1. Search Filter ---
            const matchSearch = p.product_name.toLowerCase().includes(state.search.toLowerCase());

            // --- 2. Category Filter (Fixed for Men vs Women) ---
            let matchCat = false;
            
            if (state.category === 'all') {
                matchCat = true;
            } else {
                const prodCat = p.category ? p.category.toLowerCase() : '';
                const selectedCat = state.category.toLowerCase();

                if (selectedCat === 'men') {
                    // If filtering for "Men", it must NOT contain "Women"
                    matchCat = prodCat.includes('men') && !prodCat.includes('women');
                } else {
                    // Standard check for other categories
                    matchCat = prodCat.includes(selectedCat);
                }
            }

            return matchCat && matchSearch;
        });

        // --- Sorting Logic ---
        if (state.sort === 'low-high') {
            filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
        } else if (state.sort === 'high-low') {
            filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
        } else {
            filtered.sort((a, b) => b.id - a.id);
        }

        // --- Display Logic ---
        if (filtered.length === 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
            filtered.forEach(p => {
                // Ensure Image URL works
                let imgSrc = p.image_url ? p.image_url : 'https://via.placeholder.com/300';
                
                container.innerHTML += `
                    <div class="col-md-6 col-lg-3 animation-fade">
                        <div class="shoe-card h-100 d-flex flex-column">
                            <span class="badge-cat">${p.category}</span>
                            <div class="img-wrapper">
                                <img src="${imgSrc}" alt="${p.product_name}" onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                            </div>
                            <div class="shoe-body flex-grow-1 d-flex flex-column">
                                <div class="mb-auto">
                                    <div class="shoe-title" title="${p.product_name}">${p.product_name}</div>
                                    <div class="price-tag">RM ${parseFloat(p.price).toFixed(2)}</div>
                                </div>
                                <button class="btn-view" onclick="openModal(${p.id})">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>`;
            });
        }
    }

    // 3. Filter Functions
    function setCategory(cat, btn) {
        state.category = cat;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        render();
    }
    function setSearch(val) { state.search = val; render(); }
    function setSort(val) { state.sort = val; render(); }

    // 4. Modal Logic
    function openModal(id) {
        const p = products.find(item => item.id == id);
        
        let imgSrc = p.image_url ? p.image_url : 'https://via.placeholder.com/300';

        document.getElementById('modalImg').src = imgSrc;
        document.getElementById('modalName').innerText = p.product_name;
        document.getElementById('modalCat').innerText = p.category;
        document.getElementById('modalPrice').innerText = "RM " + parseFloat(p.price).toFixed(2);
        document.getElementById('modalDesc').innerText = p.description || "No description available.";
        document.getElementById('modalId').value = p.id;

        // Generate Size Radios
        const sizeContainer = document.getElementById('modalSizes');
        if(p.sizes && p.sizes.length > 0) {
            sizeContainer.innerHTML = p.sizes.map((s, index) => `
                <input type="radio" class="size-radio" name="size" id="size-${index}" value="${s}" ${index===0 ? 'checked' : ''}>
                <label class="size-label" for="size-${index}">${s}</label>
            `).join('');
        } else {
            sizeContainer.innerHTML = '<p class="text-danger">Standard Size</p><input type="hidden" name="size" value="Standard">';
        }

        productModal.show();
    }

    // 5. Add To Cart Logic
    function triggerAddToCart() {
        const id = document.getElementById('modalId').value;
        const qty = document.getElementById('modalQty').value;
        
        // Get selected size
        const sizeInput = document.querySelector('input[name="size"]:checked');
        const size = sizeInput ? sizeInput.value : 'Standard';

        // Create Form Data
        const formData = new FormData();
        formData.append('product_id', id);
        formData.append('quantity', qty);
        formData.append('size', size);

        // Fetch request
        fetch('addtocart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'login_required') {
                alert("Please log in first.");
                window.location.href = "custloginandregister.php";
                return;
            }

            if(data.success) {
                alert('Successfully added to cart!');
                productModal.hide();
            } else {
                alert('Failed to add to cart: ' + (data.message || ''));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error connecting to server.');
        });
    }

    // Initial Render
    render();
</script>
</body>
</html>