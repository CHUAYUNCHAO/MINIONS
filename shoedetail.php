<?php
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
<style>

    :root {

        --primary-color: black;

        --secondary-color: #2575fc;

        --success-color: #2ecc71;

        --danger-color: #e74c3c;

        --warning-color: #f39c12;

        --dark-text: #2c3e50;

    }



    body {

        font-family: 'Segoe UI', sans-serif;

        background: white;

        padding-bottom: 50px;

    }



    /* --- HERO HEADER --- */

    .custom-header {

        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));

        color: white;

        padding: 3rem 2rem;

        margin-bottom: 2rem;

        border-radius: 0 0 20px 20px;

        box-shadow: 0 10px 30px rgba(37, 117, 252, 0.3);

        text-align: center;

    }



    .custom-header h1 {

        font-weight: 800;

        letter-spacing: 1px;

        margin-bottom: 10px;

    }



    /* --- FILTERS --- */

    .filter-tabs {

        display: flex;

        justify-content: center;

        gap: 15px;

        margin-bottom: 30px;

        flex-wrap: wrap;

    }



    .filter-btn {

        background: white;

        border: 2px solid #eee;

        padding: 10px 25px;

        border-radius: 30px;

        font-weight: 600;

        color: #555;

        cursor: pointer;

        transition: 0.3s;

    }



    .filter-btn:hover, .filter-btn.active {

        background: var(--primary-color);

        color: white;

        border-color: var(--primary-color);

        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);

    }



    /* --- SHOE CARD --- */

    .shoe-card {

        background: white;

        border-radius: 15px;

        overflow: hidden;

        box-shadow: 0 5px 20px rgba(0,0,0,0.05);

        transition: 0.3s;

        height: 100%;

        display: flex;

        flex-direction: column;

        position: relative;

    }



    .shoe-card:hover {

        transform: translateY(-10px);

        box-shadow: 0 15px 30px rgba(0,0,0,0.15);

    }



    .img-wrapper {

        height: 220px;

        overflow: hidden;

        background: #f8f9fa;

        display: flex;

        align-items: center;

        justify-content: center;

    }



    .shoe-card img {

        width: 100%;

        height: 100%;

        object-fit: cover;

        transition: 0.5s;

    }



    .shoe-card:hover img {

        transform: scale(1.1);

    }



    .shoe-body {

        padding: 20px;

        flex: 1;

        display: flex;

        flex-direction: column;

    }



    .category-badge {

        font-size: 0.75rem;

        text-transform: uppercase;

        letter-spacing: 1px;

        color: #888;

        font-weight: 700;

        margin-bottom: 5px;

    }



    .shoe-title {

        font-size: 1.1rem;

        font-weight: 700;

        color: var(--dark-text);

        margin-bottom: 10px;

    }



    .price-tag {

        font-size: 1.25rem;

        color: var(--success-color);

        font-weight: 800;

        margin-bottom: 15px;

    }



    .btn-view {

        margin-top: auto;

        width: 100%;

        background: white;

        border: 2px solid var(--primary-color);

        color: var(--primary-color);

        padding: 10px;

        border-radius: 8px;

        font-weight: 600;

        transition: 0.3s;

    }



    .btn-view:hover {

        background: var(--primary-color);

        color: white;

    }



    /* --- MODAL STYLING --- */

    .modal-content {

        border-radius: 15px;

        border: none;

        overflow: hidden;

    }



    .modal-header {

        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));

        color: white;

    }



    .size-option, .color-option {

        cursor: pointer;

        display: inline-block;

        margin: 5px;

        border: 2px solid #ddd;

        transition: 0.2s;

    }



    .size-option {

        padding: 8px 12px;

        border-radius: 5px;

        font-weight: 600;

    }



    .size-option.active {

        background: var(--dark-text);

        color: white;

        border-color: var(--dark-text);

    }



    .color-option {

        width: 30px;

        height: 30px;

        border-radius: 50%;

    }



    .color-option.active {

        border-color: var(--dark-text);

        transform: scale(1.2);

    }

    

    /* Admin Box */

    .admin-box {

        background: white;

        border-radius: 15px;

        padding: 30px;

        margin-top: 50px;

        border-left: 5px solid var(--warning-color);

        box-shadow: 0 5px 20px rgba(0,0,0,0.05);

    }



    /* Success Toast */

    .toast-custom {

        position: fixed;

        bottom: 20px;

        right: 20px;

        background: var(--success-color);

        color: white;

        padding: 15px 25px;

        border-radius: 10px;

        box-shadow: 0 10px 20px rgba(46, 204, 113, 0.3);

        transform: translateY(100px);

        transition: 0.3s;

        z-index: 1050;

        display: flex;

        align-items: center;

        gap: 10px;

    }

    .toast-custom.show {

        transform: translateY(0);

    }



</style>

    <meta charset="UTF-8">
    <title>Product Details | ShoeHaven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (Your existing CSS goes here) */
    </style>
</head>
<body>

    <div class="custom-header">
        <div class="container">
            <h1><i class="fas fa-shoe-prints"></i> ShoeHaven Product Details</h1>
            <p>Explore our premium collection with detailed specifications.</p>
        </div>
    </div>

    <div class="container">
        <div class="filter-tabs">
            <button class="filter-btn active" onclick="filterProducts('all')">All Shoes</button>
            <button class="filter-btn" onclick="filterProducts('men')">Men's</button>
            <button class="filter-btn" onclick="filterProducts('women')">Women's</button>
            <button class="filter-btn" onclick="filterProducts('kids')">Kids'</button>
        </div>

        <div class="row g-4" id="productContainer">
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // --- 1. DATA: Injecting PHP data into JavaScript ---
    const products = <?php echo json_encode($products); ?>;

    // --- 2. Render Logic ---
    function filterProducts(type) {
        // Clear active states
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        
        const container = document.getElementById('productContainer');
        container.innerHTML = '';
        
        // Filter based on 'group_name' from the database
        const filtered = type === 'all' ? products : products.filter(p => p.group_name === type);

        filtered.forEach(product => {
            const html = `
                <div class="col-md-6 col-lg-3">
                    <div class="shoe-card">
                        <div class="img-wrapper">
                            <img src="${product.image_url}" alt="${product.product_name}">
                        </div>
                        <div class="shoe-body">
                            <div class="category-badge">${product.category}</div>
                            <div class="shoe-title">${product.product_name}</div>
                            <div class="price-tag">RM ${parseFloat(product.price).toFixed(2)}</div>
                            <button class="btn-view" onclick="openModal(${product.id})">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    }

    // --- 3. Modal Logic ---
    let currentModalProduct = null;
    const modal = new bootstrap.Modal(document.getElementById('productModal'));

    function openModal(id) {
        const product = products.find(p => p.id == id);
        currentModalProduct = product;

        document.getElementById('modalTitle').innerText = product.product_name;
        document.getElementById('modalName').innerText = product.product_name;
        document.getElementById('modalCat').innerText = product.category;
        document.getElementById('modalPrice').innerText = "RM " + parseFloat(product.price).toFixed(2);
        document.getElementById('modalDesc').innerText = product.description;
        document.getElementById('modalImg').src = product.image_url;

        // Generate Size Buttons from array
        let sizesHtml = '';
        product.sizes.forEach(size => {
            sizesHtml += `<span class="size-option" onclick="selectOption(this, 'size')">${size}</span>`;
        });
        document.getElementById('modalSizes').innerHTML = sizesHtml;

        // Generate Color Buttons from array
        let colorsHtml = '';
        product.colors.forEach(color => {
            colorsHtml += `<span class="color-option" onclick="selectOption(this, 'color')" style="background:${color}"></span>`;
        });
        document.getElementById('modalColors').innerHTML = colorsHtml;

        modal.show();
    }

    function selectOption(element, type) {
        let siblings = element.parentElement.children;
        for(let sib of siblings) sib.classList.remove('active');
        element.classList.add('active');
    }

    function addToCart() {
        const sizeSelected = document.querySelector('.size-option.active');
        const colorSelected = document.querySelector('.color-option.active');

        if(!sizeSelected || !colorSelected) {
            alert("Please select a size and color!");
            return;
        }

        modal.hide();
        const toast = document.getElementById('successToast');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Initial Load
    filterProducts('all');
</script>
</body>
</html>