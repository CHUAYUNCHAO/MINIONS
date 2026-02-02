<?php
session_start();
// Include the database configuration if you want to pull stats from the DB later
// include 'db_config.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | ShoeHaven</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    header {
    background: white;
    padding: 15px 40px;
    display: flex; /* Mandatory for side-by-side layout */
    justify-content: space-between; /* Pushes brand to left, nav to center, icons to right */
    align-items: center; /* Centers items vertically */
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
}

/* Ensure icons/buttons stay together on the right */
.nav-icons {
    display: flex;
    align-items: center;
    gap: 20px; /* Adds space between Cart and Log Out */
}

:root {
    --primary-color: #111;
    --accent-color: #ff6b6b; 
    --bg-color: #f8f9fa;
    --text-dark: #333;
    --text-muted: #666;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background-color: var(--bg-color);
    color: var(--text-dark);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* --- HERO SECTION --- */
.about-hero {
    background: url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=1600&q=80') no-repeat center center/cover;
    height: 400px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    margin-bottom: 50px;
}

/* Dark Overlay for better text readability */
.about-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}

.hero-content {
    position: relative;
    z-index: 1;
    padding: 0 20px;
}

.about-hero h1 { 
    font-size: 3.5rem; 
    font-weight: 800; 
    letter-spacing: 2px; 
    margin-bottom: 15px; 
}

.about-hero p { 
    font-size: 1.2rem; 
    opacity: 0.95; 
    max-width: 700px; 
    margin: 0 auto; 
}

/* --- CONTENT TITLES --- */
.section-title {
    text-align: center;
    font-weight: 800;
    margin-bottom: 40px;
    position: relative;
    display: inline-block;
    left: 50%;
    transform: translateX(-50%);
    color: var(--primary-color);
}

.section-title::after {
    content: '';
    display: block;
    width: 60px;
    height: 4px;
    background: var(--accent-color);
    margin: 10px auto 0;
    border-radius: 2px;
}

.story-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-muted);
    margin-bottom: 20px;
}

/* --- STATS CARDS --- */
.stats-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-box {
    flex: 1;
    text-align: center;
    background: white;
    padding: 30px 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    min-width: 150px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-bottom: 4px solid var(--accent-color);
}

.stat-box:hover { 
    transform: translateY(-5px); 
    box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
}

.stat-number { 
    font-size: 2.2rem; 
    font-weight: 800; 
    color: var(--accent-color); 
    display: block; 
    margin-bottom: 5px; 
}

.stat-label { 
    font-weight: 600; 
    color: #888; 
    text-transform: uppercase; 
    letter-spacing: 1px; 
    font-size: 0.85rem; 
}

/* --- TEAM SQUAD CARDS --- */
.team-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    text-align: center;
    height: 100%;
}

.team-card:hover { 
    transform: translateY(-10px); 
    box-shadow: 0 15px 30px rgba(0,0,0,0.1); 
}

.team-img-wrapper {
    height: 280px;
    overflow: hidden;
    position: relative;
}

.team-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.team-card:hover .team-img { 
    transform: scale(1.1); 
}

.team-info { padding: 20px; }

.member-name { 
    font-weight: 700; 
    font-size: 1.25rem; 
    color: var(--primary-color); 
    margin-bottom: 5px; 
}

.member-role { 
    color: var(--accent-color); 
    font-weight: 600; 
    font-size: 0.9rem; 
    text-transform: uppercase; 
}

/* --- FOOTER --- */
footer {
    background: var(--primary-color);
    color: #ccc;
    text-align: center;
    padding: 40px 20px;
    margin-top: auto;
}

.social-icons a {
    color: white;
    margin: 0 10px;
    font-size: 1.2rem;
    transition: color 0.3s;
}

.social-icons a:hover { color: var(--accent-color); }
    </style>
</head>
<body>

  <header>
    <div class="brand">🍌 MINION SHOE</div>
    <nav>
      <a href="homeindex.php">Home</a>
            <a href="catelouge.php" class="active">Shop</a>
            <a href="shoedetail.php">Detail</a>
            <a href="aboutus.php">About</a>
    </nav>
    <div class="nav-icons">
        <a href="cart.php">🛒 Cart</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" style="color: red; font-weight: bold; margin-left: 20px;">Log Out</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Login</a>
        <?php endif; ?>
    </div>
</header>

    <div class="about-hero">
        <div class="hero-content">
            <h1>Who We Are</h1>
            <p>More than just a shoe store. We are a community of sneaker enthusiasts dedicated to style, culture, and comfort.</p>
        </div>
    </div>

    <div class="container content-section">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="section-title" style="display:block; text-align:left; left:0; transform:none;">Our Journey</h2>
                <p class="story-text">
                    Founded in 2024, <strong>ShoeHaven</strong> started in a small home office with a simple mission...
                </p>
            </div>
            <div class="col-lg-6">
                <div class="stats-container">
                    <div class="stat-box">
                        <span class="stat-number" data-target="50000">0</span>
                        <span class="stat-label">Happy Customers</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-number" data-target="120">0</span>
                        <span class="stat-label">Top Brands</span>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="section-title mt-5">Meet The Squad</h2>
        <div class="row g-4" id="team-container">
            </div>
    </div>

    <footer>
          <p>&copy; 2026 Minion Shoe Inc. All Rights Reserved.</p>

        <div class="social-icons mt-3">

<p style="margin-top:10px; font-size:0.8rem;">
            Follow us on <a href="https://youtu.be/dQw4w9WgXcQ?si=WHiTSICEQ8Md3R-p" class="text-white">Instagram</a> | <a href="https://youtu.be/dQw4w9WgXcQ?si=WHiTSICEQ8Md3R-p" class="text-white">Facebook</a>
        </p>

        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- 1. Animation for Stats ---
        function animateStats() {
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target'));
                const increment = target / 100;
                let current = 0;

                const updateCount = () => {
                    if (current < target) {
                        current += increment;
                        stat.innerText = Math.ceil(current) + (target > 1000 ? '+' : '');
                        setTimeout(updateCount, 20);
                    } else {
                        stat.innerText = target + (target > 1000 ? '+' : '');
                    }
                };
                updateCount();
            });
        }

        // --- 2. Scroll Animation Observer ---
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelector('.stats-container').forEach ? null : observer.observe(document.querySelector('.stats-container'));

        // --- 3. Simple Click feedback ---
        document.querySelectorAll('.team-card').forEach(card => {
            card.addEventListener('click', () => {
                console.log("Team member clicked!");
            });
        });
    </script>
</body>
</html>