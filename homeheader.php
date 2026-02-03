<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minion Shoe | Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Paste all your CSS from the original <style> block here */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; color: #333; line-height: 1.6; }
        /* ... existing styles ... */
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
        <nav>
        <a href="cart.php">🛒 Cart</a></nav>



        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="btn-login">Profile</a>
        <?php else: ?>
            <a href="custloginandregister.php" class="btn-login">Login</a>
        <?php endif; ?>
    <a href="logout.php" style="color: red; font-weight: bold; margin-left: 20px;">Log Out</a>
</div>
    </div>
</header>