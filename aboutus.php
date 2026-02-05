<?php
session_start();
require_once 'Minionshoesconfig.php';
// We don't include homeheader.php here because we build a custom transparent header for this page's "perfect" look.
// But we keep the logic consistent.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Minion Shoe</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root { --primary: #111; --accent: #ff6b6b; --bg: #f9f9f9; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: #333; overflow-x: hidden; }
        
        /* Navigation (Sticky White) */
        .navbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 2px 15px rgba(0,0,0,0.05); padding: 15px 0; }
        .brand { font-size: 1.5rem; font-weight: 800; letter-spacing: 1px; color: #111; text-decoration: none; }
        .nav-link { font-weight: 600; color: #555 !important; margin: 0 10px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--accent) !important; }

        /* Hero Section */
        .hero-section {
            height: 60vh;
            min-height: 400px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1556906781-9a412961c28c?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }
        .hero-title { font-size: 4rem; font-weight: 900; letter-spacing: -2px; margin-bottom: 20px; }
        .hero-subtitle { font-size: 1.2rem; font-weight: 300; max-width: 600px; margin: 0 auto; opacity: 0.9; }

        /* Stats Counter */
        .counter-box { background: white; padding: 40px 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid var(--accent); transition: 0.3s; }
        .counter-box:hover { transform: translateY(-10px); }
        .counter-num { font-size: 3rem; font-weight: 800; color: var(--primary); display: block; }
        .counter-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #777; font-weight: 700; }

        /* Core Values */
        .value-card { text-align: center; padding: 30px; }
        .icon-box { width: 80px; height: 80px; background: #fff0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--accent); font-size: 2rem; transition: 0.3s; }
        .value-card:hover .icon-box { background: var(--accent); color: white; transform: rotateY(180deg); }

        /* Timeline */
        .timeline { position: relative; max-width: 800px; margin: 50px auto; }
        .timeline::after { content: ''; position: absolute; width: 4px; background-color: #ddd; top: 0; bottom: 0; left: 50%; margin-left: -2px; }
        .timeline-item { padding: 10px 40px; position: relative; background-color: inherit; width: 50%; }
        .timeline-item::after { content: ''; position: absolute; width: 20px; height: 20px; right: -10px; background-color: white; border: 4px solid var(--accent); top: 15px; border-radius: 50%; z-index: 1; }
        .left { left: 0; text-align: right; }
        .right { left: 50%; text-align: left; }
        .right::after { left: -10px; }
        .content { padding: 20px; background-color: white; border-radius: 6px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        /* Team Section */
        .team-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; position: relative; }
        .team-card:hover { transform: translateY(-10px); }
        .team-img { width: 100%; height: 350px; object-fit: cover; filter: grayscale(20%); transition: 0.3s; }
        .team-card:hover .team-img { filter: grayscale(0%); }
        .team-info { padding: 20px; text-align: center; position: relative; z-index: 2; background: white; }
        .social-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 350px; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; gap: 15px; opacity: 0; transition: 0.3s; }
        .team-card:hover .social-overlay { opacity: 1; }
        .social-btn { width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); transition: 0.3s; }
        .social-btn:hover { background: var(--accent); color: white; }

        /* FAQ Accordion */
        .accordion-button:not(.collapsed) { color: var(--accent); background-color: #fff0f0; }
        .accordion-button:focus { box-shadow: none; border-color: var(--accent); }

        /* Responsive Fixes */
        @media screen and (max-width: 600px) {
            .timeline::after { left: 31px; }
            .timeline-item { width: 100%; padding-left: 70px; padding-right: 25px; }
            .timeline-item::after { left: 21px; }
            .left { text-align: left; }
            .hero-title { font-size: 2.5rem; }
        }
        footer { background: #000; color: #888; padding: 60px 0 20px; }
        .footer-title { color: white; font-weight: 800; margin-bottom: 20px; text-decoration: none; }
        .footer a { color: #888; text-decoration: none; transition: 0.3s; display: block; margin-bottom: 10px; }
        .footer a:hover { color: #ff6b6b; }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a href="homeindex.php" class="brand">🍌 MINION SHOE</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="homeindex.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="catelouge.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="shoedetail.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link active" href="aboutus.php">About</a></li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="cart.php" class="text-dark"><i class="fas fa-shopping-bag fa-lg"></i></a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn btn-dark rounded-pill px-4 btn-sm">My Account</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container" data-aos="zoom-in">
            <h1 class="hero-title">NOT JUST A SHOE.</h1>
            <p class="hero-subtitle">We are a movement of sneaker enthusiasts, dedicated to bringing premium style and comfort to minions everywhere.</p>
        </div>
    </header>

    <section class="container" style="margin-top: -50px; position: relative; z-index: 2;">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="counter-box">
                    <span class="counter-num" data-target="2020">0</span>
                    <span class="counter-label">Established</span>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="counter-box">
                    <span class="counter-num" data-target="5000">0</span>
                    <span class="counter-label">Happy Customers</span>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="counter-box">
                    <span class="counter-num" data-target="150">0</span>
                    <span class="counter-label">Premium Brands</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 my-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h5 class="text-muted text-uppercase fw-bold">Why Choose Us</h5>
                <h2 class="fw-bold display-6">Our Core Values</h2>
            </div>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card">
                        <div class="icon-box"><i class="fas fa-medal"></i></div>
                        <h4>Authenticity</h4>
                        <p class="text-muted">Every pair is verified authentic. We have zero tolerance for fakes in our inventory.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card">
                        <div class="icon-box"><i class="fas fa-shipping-fast"></i></div>
                        <h4>Speed</h4>
                        <p class="text-muted">We know you want your kicks fast. We ship within 24 hours of verification.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-card">
                        <div class="icon-box"><i class="fas fa-users"></i></div>
                        <h4>Community</h4>
                        <p class="text-muted">We build relationships, not just customers. Join the Minion Shoe family today.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center fw-bold mb-5" data-aos="fade-up">Our Journey</h2>
            <div class="timeline">
                <div class="timeline-item left" data-aos="fade-right">
                    <div class="content">
                        <h4 class="fw-bold">2020</h4>
                        <p>Founded in a small garage with just 50 pairs of shoes and a dream.</p>
                    </div>
                </div>
                <div class="timeline-item right" data-aos="fade-left">
                    <div class="content">
                        <h4 class="fw-bold">2021</h4>
                        <p>Launched our first online store and reached 1,000 customers locally.</p>
                    </div>
                </div>
                <div class="timeline-item left" data-aos="fade-right">
                    <div class="content">
                        <h4 class="fw-bold">2023</h4>
                        <p>Opened our flagship store in Minion City and partnered with major brands.</p>
                    </div>
                </div>
                <div class="timeline-item right" data-aos="fade-left">
                    <div class="content">
                        <h4 class="fw-bold">Today</h4>
                        <p>Serving thousands of customers worldwide with a curated collection of premium sneakers.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h5 class="text-muted text-uppercase fw-bold">The Brains</h5>
            <h2 class="fw-bold display-6">Meet The Squad</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="team-card">
                    <div class="social-overlay">
                        <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                    </div>
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=500&q=80" class="team-img">
                    <div class="team-info">
                        <h5 class="fw-bold mb-1">Chua Yun Chao</h5>
                        <small class="text-muted text-uppercase fw-bold">Founder & CEO</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="team-card">
                    <div class="social-overlay">
                        <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-dribbble"></i></a>
                    </div>
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&q=80" class="team-img">
                    <div class="team-info">
                        <h5 class="fw-bold mb-1">Cindy Tiong</h5>
                        <small class="text-muted text-uppercase fw-bold">Head of Design</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="team-card">
                    <div class="social-overlay">
                        <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-tiktok"></i></a>
                    </div>
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=500&q=80" class="team-img">
                    <div class="team-info">
                        <h5 class="fw-bold mb-1">Choi Zhong Bao</h5>
                        <small class="text-muted text-uppercase fw-bold">Marketing Lead</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white mb-5">
        <div class="container" style="max-width: 800px;">
            <h2 class="text-center fw-bold mb-5" data-aos="fade-up">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion" data-aos="fade-up">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Are your shoes 100% authentic?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Absolutely. Every pair of shoes sold on Minion Shoe goes through a rigorous multi-point verification process by our expert staff.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Do you ship internationally?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! We ship to over 50 countries worldwide. Shipping rates will be calculated at checkout based on your location.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            What is your return policy?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We accept returns within 30 days of purchase, provided the shoes are unworn and in their original packaging with tags attached.
                        </div>
                    </div>
                </div>
            </div>
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
        AOS.init();

        // Counter Animation Logic
        const counters = document.querySelectorAll('.counter-num');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / 100;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target + (target > 2000 ? '+' : '');
                }
            };
            // Trigger animation only when scrolled into view (Simple Timeout for demo)
            setTimeout(updateCount, 1000); 
        });
    </script>
</body>
</html>