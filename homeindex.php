<?php
session_start();
require_once 'Minionshoesconfig.php';

// 1. Fetch Trending Products
$sql = "SELECT * FROM allproducts ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Minion Shoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #fff; overflow-x: hidden; }
        
        /* Navigation (Integrated for "Perfect" look) */
        .navbar { background: white; padding: 15px 0; box-shadow: 0 2px 15px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .brand { font-size: 1.8rem; font-weight: 800; letter-spacing: 1px; color: #111; text-decoration: none; }
        .nav-link { font-weight: 600; color: #555 !important; margin: 0 10px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: #ff6b6b !important; }
        
        /* Hero Carousel */
        .carousel-item { height: 85vh; min-height: 500px; background: #000; }
        .carousel-item img { object-fit: cover; opacity: 0.6; width: 100%; height: 100%; }
        .carousel-caption { bottom: 35%; text-align: left; left: 10%; right: 10%; }
        .hero-title { font-size: 4rem; font-weight: 900; text-transform: uppercase; line-height: 1; margin-bottom: 20px; animation: slideInLeft 1s ease; }
        .hero-text { font-size: 1.3rem; font-weight: 300; margin-bottom: 30px; animation: fadeIn 1.5s ease; }
        .btn-cta { padding: 15px 40px; background: #ff6b6b; color: white; border: none; font-weight: 700; text-transform: uppercase; border-radius: 50px; transition: 0.3s; animation: slideInUp 1s ease; text-decoration: none; display: inline-block; }
        .btn-cta:hover { background: white; color: #ff6b6b; transform: translateY(-3px); }

        /* Categories Section */
        .cat-card { position: relative; overflow: hidden; border-radius: 15px; height: 300px; cursor: pointer; transition: 0.3s; }
        .cat-card img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .cat-card:hover img { transform: scale(1.1); }
        .cat-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); display: flex; align-items: flex-end; padding: 30px; }
        .cat-title { color: white; font-weight: 800; font-size: 1.5rem; text-transform: uppercase; margin: 0; }

        /* Features / Why Us */
        .feature-box { text-align: center; padding: 40px 20px; border: 1px solid #f0f0f0; border-radius: 10px; transition: 0.3s; }
        .feature-box:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.05); transform: translateY(-5px); }
        .feature-icon { font-size: 2.5rem; color: #ff6b6b; margin-bottom: 20px; }

        /* Product Cards */
        .product-card { border: none; transition: 0.3s; background: white; }
        .product-card:hover { transform: translateY(-10px); }
        .card-img-wrapper { background: #f8f9fa; height: 280px; display: flex; align-items: center; justify-content: center; border-radius: 15px; position: relative; overflow: hidden; }
        .card-img-wrapper img { max-width: 90%; max-height: 90%; transition: 0.3s; mix-blend-mode: multiply; }
        .product-card:hover img { transform: scale(1.1) rotate(-5deg); }
        .badge-new { position: absolute; top: 15px; left: 15px; background: #111; color: white; padding: 5px 12px; font-size: 0.7rem; font-weight: 800; border-radius: 20px; }
        
        .product-info { padding: 20px 5px; }
        .p-cat { color: #888; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
        .p-title { font-weight: 800; font-size: 1.1rem; color: #111; margin: 5px 0; }
        .p-price { color: #ff6b6b; font-weight: 700; font-size: 1.1rem; }

        /* Newsletter */
        .newsletter { background: #111; color: white; padding: 80px 0; text-align: center; }
        .newsletter-input { max-width: 500px; margin: 0 auto; display: flex; gap: 10px; }
        .newsletter input { flex: 1; padding: 15px; border-radius: 50px; border: none; outline: none; }
        .newsletter button { padding: 15px 30px; border-radius: 50px; border: none; background: #ff6b6b; color: white; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .newsletter button:hover { background: white; color: #111; }

        /* Footer */
        footer { background: #000; color: #888; padding: 60px 0 20px; }
        .footer-title { color: white; font-weight: 800; margin-bottom: 20px; }
        .footer a { color: #888; text-decoration: none; transition: 0.3s; display: block; margin-bottom: 10px; }
        .footer a:hover { color: #ff6b6b; }

        /* Animations */
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="homeindex.php" class="brand">🍌 MINION SHOE</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="homeindex.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="catelouge.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="shoedetail.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.php">About</a></li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="cart.php" class="text-dark"><i class="fas fa-shopping-bag fa-lg"></i></a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn btn-dark rounded-pill px-4 btn-sm">My Account</a>
                <?php else: ?>
                    <a href="custloginandregister.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1556906781-9a412961c28c?q=80&w=2000&auto=format&fit=crop" alt="Sneakers">
                <div class="carousel-caption">
                    <h1 class="hero-title">Step Into<br>Greatness</h1>
                    <p class="hero-text">Discover the latest collection of premium sneakers designed for style and comfort.</p>
                    <a href="catelouge.php" class="btn-cta">Shop Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1512374382149-233c42b6a83b?q=80&w=2000&auto=format&fit=crop" alt="Run">
                <div class="carousel-caption">
                    <h1 class="hero-title">Limitless<br>Performance</h1>
                    <p class="hero-text">Engineered for speed. Built for the grind. Unleash your potential.</p>
                    <a href="catelouge.php" class="btn-cta">Explore Sport</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <div class="container my-5 py-5" data-aos="fade-up">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-truck feature-icon"></i>
                    <h4>Free Shipping</h4>
                    <p class="text-muted">On all orders over RM 200. We deliver happiness to your doorstep.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-undo feature-icon"></i>
                    <h4>Easy Returns</h4>
                    <p class="text-muted">30-day return policy. If it doesn't fit, we'll make it right.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h4>Secure Payment</h4>
                    <p class="text-muted">100% secure payment gateways for peace of mind.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5" data-aos="fade-up">
        <h2 class="text-center fw-bold mb-5">Shop By Category</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="cat-card" onclick="location.href='catelouge.php'">
                    <img src="https://images.unsplash.com/photo-1617606002779-51d866bdd1d1?auto=format&fit=crop&w=500&q=80" alt="Men">
                    <div class="cat-overlay">
                        <h3 class="cat-title">Men</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="cat-card" onclick="location.href='catelouge.php'">
                    <img src="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=500&q=80" alt="Women">
                    <div class="cat-overlay">
                        <h3 class="cat-title">Women</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="cat-card" onclick="location.href='catelouge.php'">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRSoGb3vgLqR3XYjoDPeBd0ox27PqxbUwYFpg&s" alt="Kids">
                    <div class="cat-overlay">
                        <h3 class="cat-title">Kids</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold m-0">Trending Now <span style="color:#ff6b6b">🔥</span></h2>
            <a href="catelouge.php" class="text-decoration-none fw-bold text-dark">View All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($product = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="product-card h-100" onclick="location.href='shoedetail.php'">
                            <div class="card-img-wrapper">
                                <span class="badge-new">NEW</span>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Shoe">
                            </div>
                            <div class="product-info">
                                <div class="p-cat"><?php echo htmlspecialchars($product['category']); ?></div>
                                <div class="p-title"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                <div class="p-price">RM <?php echo number_format($product['price'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No trending products available right now.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <section class="newsletter" data-aos="fade-up">
        <div class="container">
            <h2 class="fw-bold mb-3">Join the Club</h2>
            <p class="mb-4 text-white-50">Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.</p>
            <form class="newsletter-input" onsubmit="event.preventDefault(); alert('Subscribed!');">
                <input type="email" placeholder="Enter your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h4 class="text-white fw-bold mb-4">🍌 MINION SHOE</h4>
                    <p>The premier destination for sneaker enthusiasts. We bring you the latest drops and exclusive styles from top brands.</p>
                    <div class="mt-4">
                        <a href="https://www.facebook.com/share/1Aob66BUNA/" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="https://www.instagram.com/minionshoemy?igsh=MTRjdG8zcm1wang4YQ==" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h5 class="footer-title">Shop</h5>
                    <a href="catelouge.php">Men</a>
                    <a href="catelouge.php">Women</a>
                    <a href="catelouge.php">Kids</a>
                    <a href="catelouge.php">New Arrivals</a>
                </div>
                <div class="col-lg-2 col-6">
                    <h5 class="footer-title">Support</h5>
                    <a href="#">Contact Us</a>
                    <a href="#">FAQ</a>
                    <a href="#">Shipping</a>
                    <a href="#">Returns</a>
                </div>
                <div class="col-lg-4">
                    <h5 class="footer-title">Get in Touch</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Banana Street, Minion City</p>
                    <p><i class="fas fa-envelope me-2"></i> hello@minionshoe.com</p>
                    <p><i class="fas fa-phone me-2"></i> +60 12-345 6789</p>
                </div>
            </div>
            <hr class="border-secondary my-5">
            <div class="text-center small">
                &copy; <?php echo date('Y'); ?> Minion Shoe. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>