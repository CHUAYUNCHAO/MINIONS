<?php
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "minion_shoe_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Fetch all products
$result = $conn->query("SELECT * FROM catelog");
$all_products = [];
while($row = $result->fetch_assoc()) {
    $all_products[] = $row;
}

// 3. Helper function to render a category section
function renderCategory($group, $products) {
    foreach ($products as $product) {
        if ($product['category_group'] !== $group) continue;
        
        $colorArray = explode(',', $product['colors']); // Convert string to array for spans
        ?>
        <div class="col-md-4">
            <div class="product-card">
                <div class="img-wrapper">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Shoe">
                </div>
                <div class="product-body">
                    <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="desc"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="price">RM <?php echo number_format($product['price'], 2); ?></p>
                    <div class="color-view mb-3">
                        <?php foreach($colorArray as $color): ?>
                            <span style="background:<?php echo trim($color); ?>"></span>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn btn-custom w-100" 
                            onclick="openModal('<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                        Select Options
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details | ShoeHaven</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* --- USER CSS CODE --- */
        body{ background:#f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; padding-bottom: 50px; }
        .page-header { background: white; padding: 30px 0; margin-bottom: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; }
        .brand-title { font-weight: 800; letter-spacing: 2px; color: #111; margin: 0; }
        .section-title{ margin:50px 0 30px; font-weight:800; text-transform: uppercase; border-left:6px solid #ff6b6b; padding-left:15px; color: #333; }
        .product-card{ background:#fff; border: none; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.05); overflow:hidden; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; }
        .product-card:hover{ transform:translateY(-8px); box-shadow:0 12px 24px rgba(0,0,0,0.12); }
        .img-wrapper { height: 250px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; padding: 10px; }
        .product-card img{ max-height: 100%; max-width: 100%; object-fit: contain; mix-blend-mode: multiply; }
        .product-body{ padding:20px; flex: 1; display: flex; flex-direction: column; }
        .product-body h5 { font-weight: 700; font-size: 1.1rem; margin-bottom: 5px; }
        .desc { color: #666; font-size: 0.9rem; margin-bottom: 15px; flex-grow: 1; }
        .price{ color:#ff6b6b; font-size:1.3rem; font-weight:bold; margin-bottom: 10px; }
        .color-view span{ width:18px; height:18px; border-radius:50%; display:inline-block; margin-right:4px; border:1px solid #ddd; }
        .btn-custom { background-color: #111; color: white; border: none; padding: 10px; font-weight: 600; }
        .btn-custom:hover { background-color: #ff6b6b; color: white; }
        .size-btn{ padding:8px 14px; border:1px solid #ddd; border-radius:6px; margin:5px; cursor:pointer; display: inline-block; transition: 0.2s; }
        .size-btn.active{ background:#111; color:#fff; border-color: #111; }
        .color-option{ width:35px; height:35px; border-radius:50%; display:inline-block; margin-right:10px; cursor:pointer; border:2px solid #eee; }
        .color-option.active{ border:3px solid #ff6b6b; transform: scale(1.1); }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <h2 class="brand-title">Minion Shoes</h2>
        <p class="text-muted mb-0">Select your size and style</p>
    </div>
</div>

<div class="container">
    <h4 class="section-title">Men's Collection</h4>
    <div class="row g-4">
        <?php renderCategory('men', $all_products); ?>
    </div>

    <h4 class="section-title">Women's Collection</h4>
    <div class="row g-4">
        <?php renderCategory('women', $all_products); ?>
    </div>

    <h4 class="section-title">Kids' Collection</h4>
    <div class="row g-4">
        <?php renderCategory('kids', $all_products); ?>
    </div>
</div>

<div class="modal fade" id="cartModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Size & Color</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h4 id="productName" style="font-weight:800;"></h4>
        <p class="price" id="productPrice" style="color:#ff6b6b;"></p>
        <h6 class="mt-4">Select Size</h6>
        <div id="sizeContainer">
          <span class="size-btn">US 7</span><span class="size-btn">US 8</span>
          <span class="size-btn">US 9</span><span class="size-btn">US 10</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-custom">Confirm Add to Cart</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openModal(name, price){
  document.getElementById("productName").innerText = name;
  document.getElementById("productPrice").innerText = "RM " + price.toFixed(2);
  new bootstrap.Modal(document.getElementById("cartModal")).show();
}

document.addEventListener("click",e=>{
  if(e.target.classList.contains("size-btn")){
    document.querySelectorAll(".size-btn").forEach(b=>b.classList.remove("active"));
    e.target.classList.add("active");
  }
});
</script>
</body>
</html>