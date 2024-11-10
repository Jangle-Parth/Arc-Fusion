<?php
require_once 'php/db.php';
$db = Database::getInstance();

// Get all products
$products = $db->find('products', [], [
    'sort' => ['created_at' => -1],
    'projection' => [
        '_id' => 1,
        'name' => 1,
        'description' => 1,
        'price' => 1,
        'price2' => 1,
        'category' => 1,
        'colors' => 1,
        'images' => 1,
        'ratings' => 1
    ]
]);

// Generate the HTML dynamically
$productsHTML = '';
foreach ($products as $product) {
    // Default values for all possible undefined keys
    $productData = [
        'name' => isset($product['name']) ? $product['name'] : 'Untitled Product',
        'description' => isset($product['description']) ? $product['description'] : 'No description available',
        'price' => isset($product['price']) ? $product['price'] : '0',
        'price2' => isset($product['price2']) ? $product['price2'] : '',
        'images' => isset($product['images'])? $product['images'] : [],
        '_id' => isset($product['_id']) ? $product['_id'] : ''
    ];

    $productsHTML .= '
    <div class="card" onclick="handleCardClick(\'' . $productData['_id'] . '\', event)">
            <img src="' . (isset($productData['images'][0]) ? htmlspecialchars($productData['images'][0]) : 'placeholder.jpg') . '" alt="' . htmlspecialchars($productData['name']) . '">
            <div class="card-content">
                <h3>' . htmlspecialchars($productData['name']) . '</h3>
                <p class="price">' . htmlspecialchars($productData['price']) . '</p>   
                <button onclick="addToCart(\'' . $productData['_id'] . '\', event)" class="buy-button">Add to Cart</button>
            </div>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArcFusion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
    :root {
        --primary-purple: #7B6CF6;
        --light-purple: #F3F1FF;
        --dark-purple: #4A3F9F;
        --accent-purple: #9D8DF7;
        --text-dark: #2D2A45;
        --text-light: #6E6B85;
        --white: #FFFFFF;
        --success-green: #4CAF50;
        --warning-orange: #FF9800;
        --error-red: #F44336;
        --gradient-purple: linear-gradient(135deg, #7B6CF6 0%, #9D8DF7 100%);
        --shadow-sm: 0 2px 4px rgba(123, 108, 246, 0.1);
        --shadow-md: 0 4px 6px rgba(123, 108, 246, 0.15);
        --shadow-lg: 0 8px 16px rgba(123, 108, 246, 0.2);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--white);
        color: var(--text-dark);
        line-height: 1.6;
    }

    /* Header Styles */
    /* Header Styles */
    .header {
        background: var(--white);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .header-top {
        background: #4A3F9F;
        color: var(--white);
        text-align: center;
        padding: 8px;
        font-size: 0.9rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: var(--text-dark);
    }

    .logo img {
        height: 40px;
    }

    .nav-menu {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .nav-link {
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: var(--transition);
        position: relative;
    }

    .nav-link:hover {
        background: var(--background);
        color: #7B6CF6;
        ;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: #7B6CF6;
        ;
        transition: var(--transition);
    }

    .nav-link:hover::after {
        width: 80%;
    }

    .nav-icons {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .icon-btn {
        position: relative;
        background: none;
        border: none;
        color: var(--text-dark);
        cursor: pointer;
        transition: var(--transition);
    }

    .icon-btn:hover {
        color: #7B6CF6;
        ;
        transform: translateY(-2px);
    }

    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #7B6CF6;
        ;
        color: var(--white);
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: 600;
    }

    /* Mobile Menu */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-dark);
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .mobile-menu-btn {
            display: block;
        }

        .nav-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #4A3F9F;
            ;
            padding: 1rem;
            flex-direction: column;
            gap: 1rem;
            box-shadow: var(--shadow-md);
        }

        .nav-menu.active {
            display: flex;
        }

        .nav-link::after {
            display: none;
        }
    }

    .card-container,
    .categories-grid-top,
    .categories-grid-bottom {
        display: grid;
        gap: 2rem;
        justify-content: center;
        padding-bottom: 20px;
    }

    .card-container {
        grid-template-columns: repeat(3, 1fr);
        /* 3 columns layout */
    }

    .product-frame {
        width: 300px;
        position: relative;
        padding: 12px;
        background: white;
        border: 3px solid black;
        border-radius: 24px;
        cursor: pointer;
        transition: transform 0.3s ease;
        text-align: center;
    }

    .product-frame:hover {
        transform: translateY(-10px);
    }

    .product-frame img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 12px;
    }

    .label-container {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 8px 40px;
        border: 4px solid black;
        border-radius: 30px;
        white-space: nowrap;
    }

    .product-name {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
        font-family: Arial, sans-serif;
    }

    .price {
        position: absolute;
        top: 20px;
        right: 20px;
        background: black;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 18px;
    }

    .top-nav {
        background: var(--white);
        padding: 1rem;
        display: flex;
        justify-content: center;
        gap: 2rem;
    }

    .nav-link {
        position: relative;
        padding: 0.75rem 1.5rem;
        color: var(--text-dark);
        text-decoration: none;
        font-family: sans-serif;
        overflow: hidden;
        background: transparent;
        transition: color 0.3s ease;
        border-radius: 8px;
        min-width: 120px;
        text-align: center;
    }

    .nav-link:hover {
        color: var(--white);
    }

    /* Nozzle design */
    .nav-link .nozzle {
        position: absolute;
        width: 16px;
        height: 24px;
        background: var(--dark-purple);
        clip-path: polygon(0% 0%,
                100% 0%,
                100% 70%,
                50% 100%,
                0% 70%);
        z-index: 4;
        opacity: 0;
        filter: drop-shadow(0 0 2px rgba(0, 0, 0, 0.3));
    }

    .nav-link .nozzle::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        background: var(--primary-purple);
        border-radius: 50%;
        animation: drip 0.5s linear infinite;
        opacity: 0;
    }

    .nav-link:hover .nozzle {
        opacity: 1;
        animation: moveNozzle 3s linear infinite;
    }

    .nav-link:hover .nozzle::after {
        opacity: 1;
    }

    /* Progress fill */
    .nav-link .fill {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 0%;
        background: linear-gradient(135deg, var(--primary-purple), var(--accent-purple));
        z-index: 1;
        transform-origin: bottom;
    }

    .nav-link:hover .fill {
        animation: fillUp 3s linear infinite;
    }

    /* Text needs to stay on top */
    .nav-link span {
        position: relative;
        z-index: 2;
    }

    @keyframes moveNozzle {
        0% {
            left: -16px;
            bottom: 0;
        }

        15% {
            left: calc(100% + 16px);
            bottom: 0;
        }

        20% {
            left: -16px;
            bottom: 20%;
        }

        35% {
            left: calc(100% + 16px);
            bottom: 20%;
        }

        40% {
            left: -16px;
            bottom: 40%;
        }

        55% {
            left: calc(100% + 16px);
            bottom: 40%;
        }

        60% {
            left: -16px;
            bottom: 60%;
        }

        75% {
            left: calc(100% + 16px);
            bottom: 60%;
        }

        80% {
            left: -16px;
            bottom: 80%;
        }

        95% {
            left: calc(100% + 16px);
            bottom: 80%;
        }

        100% {
            left: -16px;
            bottom: 100%;
        }
    }

    @keyframes fillUp {
        0% {
            height: 0%;
            opacity: 0.9;
        }

        20% {
            height: 20%;
            opacity: 0.9;
        }

        40% {
            height: 40%;
            opacity: 0.9;
        }

        60% {
            height: 60%;
            opacity: 0.9;
        }

        80% {
            height: 80%;
            opacity: 0.9;
        }

        100% {
            height: 100%;
            opacity: 0.9;
        }
    }

    @keyframes drip {

        0%,
        100% {
            transform: translateX(-50%) scale(1);
        }

        50% {
            transform: translateX(-50%) scale(1.2);
        }
    }

    /* Footer */
    .footer-container {
        background-color: var(--light-purple);
        padding: 0 0 2rem 0;
        margin-top: 4rem;
        width: 100%;
    }

    .brand-banner {
        background-color: #6E6B85;
        color: var(--white);
        text-align: center;
        padding: 1rem 0;
        margin-bottom: 3rem;
        width: 100%;
    }

    .brand-banner h2 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .customer-count {
        font-size: 1.8rem;
        font-weight: 500;
        color: var(--white);
    }

    .customer-count span {
        font-weight: 700;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        padding: 0 2rem;
    }

    .footer-column h3 {
        color: var(--text-dark);
        font-size: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 900;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-column ul li {
        margin-bottom: 0.75rem;
    }

    .footer-column ul li a {
        color: var(--text-light);
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .footer-column ul li a:hover {
        color: var(--primary-purple);
    }

    .view-more {
        color: var(--primary-purple) !important;
        font-weight: 500;
    }

    .service-features {
        margin-top: 2rem;
    }

    .feature {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        color: var(--text-light);
        font-size: 0.9rem;
    }

    .app-section {
        text-align: center;
        margin: 3rem 0;
        padding: 0 2rem;
    }

    .app-section h3 {
        color: var(--text-dark);
        font-size: 1rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }

    .app-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .app-button img {
        height: 40px;
        border-radius: 8px;
    }

    .social-section {
        text-align: center;
        padding: 2rem 2rem 0;
        border-top: 1px solid rgba(110, 107, 133, 0.1);
        margin: 0 2rem;
    }

    .social-section p {
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        transition: transform 0.3s ease;
        text-decoration: none;
    }

    .social-icon:hover {
        transform: translateY(-2px);
    }

    .social-icon.instagram {
        background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D);
    }

    .social-icon.twitter {
        background-color: #1DA1F2;
    }

    .social-icon.youtube {
        background-color: #FD1D1D;
    }

    /* Footer Responsive Styles */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem 1rem;
        }

        .brand-banner h2 {
            font-size: 1.2rem;
        }

        .customer-count {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .footer-content {
            grid-template-columns: 1fr;
        }

        .app-buttons {
            flex-direction: column;
            align-items: center;
        }

        .social-section {
            margin: 0 1rem;
        }
    }

    /* Hero Section */
    .hero {
        margin-top: 80px;
        padding: 4rem 2rem;
        background: var(--light-purple);
        text-align: center;
    }

    .hero h1 {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        background: var(--gradient-purple);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    .hero p {
        font-size: 1.2rem;
        color: var(--text-light);
        max-width: 600px;
        margin: 0 auto 2rem;
    }

    /* Products Grid */
    .products-section {
        padding: 4rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .section-header h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--text-dark);
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
    }

    .card {
        background: var(--white);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        box-shadow: var(--shadow-sm);
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .card img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card:hover img {
        transform: scale(1.05);
    }

    .card-content {
        padding: 1.5rem;
        text-align: center;
    }

    .card h3 {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }

    .price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-purple);
        margin-bottom: 1rem;
    }

    .buy-button {
        background: var(--gradient-purple);
        color: var(--white);
        padding: 0.8rem 2rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .buy-button:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-md);
    }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-top">
            Free shipping on orders above ₹999 | Shop Now!
        </div>

        <nav class="top-nav">
            <a href="/" class="logo">
                <img src="assets/images/logo.png" alt="ArcFusion Logo">

            </a>
            <a href="index.html" class="nav-link">
                <span>Home</span>
                <div class="nozzle"></div>
                <div class="fill"></div>
            </a>
            <a href="products.html" class="nav-link">
                <span>Products</span>
                <div class="nozzle"></div>
                <div class="fill"></div>
            </a>
            <a href="about.html" class="nav-link">
                <span>About</span>
                <div class="nozzle"></div>
                <div class="fill"></div>
            </a>
            <a href="contact-us.html" class="nav-link">
                <span>Contact</span>
                <div class="nozzle"></div>
                <div class="fill"></div>
            </a>
            <div class="nav-icons">
                <button class="icon-btn">
                    <i class="fas fa-search fa-lg"></i>
                </button>
                <button class="icon-btn">
                    <a href="profile.html">
                        <i class="far fa-user fa-lg"></i>
                    </a>
                </button>
                <button class="icon-btn" id="cart-button">
                    <i class="fas fa-shopping-cart fa-lg" id="cart-icon"></i>
                    <span class="cart-count">2</span>
                </button>
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

        </nav>
    </header>

    <section class="hero">
        <h1>Elevate Your Style Game</h1>
        <p>Discover the perfect blend of comfort and contemporary fashion with our latest collection.</p>
    </section>

    <main>
        <section class="products-section">
            <div class="section-header">
                <h2>Featured Collection</h2>
            </div>
            <div class="grid">
                <?php echo $productsHTML; ?>
            </div>
        </section>
    </main>

    <footer class="footer-container">
        <!-- Brand Banner -->
        <div class="brand-banner">
            <h2>HOMEGROWN INDIAN BRAND</h2>

        </div>

        <!-- Footer Content -->
        <div class="footer-content">
            <!-- Help Section -->
            <div class="footer-column">
                <h3>NEED HELP</h3>
                <ul>
                    <li><a href="contact-us.html">Contact Us</a></li>
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Returns & Refunds</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">My Account</a></li>
                </ul>

            </div>

            <!-- Company Section -->
            <div class="footer-column">
                <h3>COMPANY</h3>
                <ul>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Gift Vouchers</a></li>
                    <li><a href="#">Community Initiatives</a></li>
                    <li><a href="#">Souled Army</a></li>
                </ul>
            </div>

            <!-- More Info Section -->
            <div class="footer-column">
                <h3>MORE INFO</h3>
                <ul>
                    <li><a href="#">T&C</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Sitemap</a></li>
                    <li><a href="#">Blogs</a></li>
                </ul>
            </div>
            <!-- Social Media Section -->
            <div class="social-section">
                <p>Follow Us:</p>
                <div class="social-icons">

                    <a href="https://www.instagram.com/arcfusionindia/" class="social-icon instagram">
                        <i class="fab fa-instagram"></i>
                    </a>

                    <a href="#" class="social-icon twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/@ArcFusionIndia2024" class="social-icon youtube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Add to Cart Animation Container -->
    <div id="cart-animation" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        pointer-events: none; z-index: 9999;"></div>

    <script>
    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Header scroll effect
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        const currentScroll = window.pageYOffset;

        if (currentScroll <= 0) {
            header.style.boxShadow = 'none';
        } else {
            header.style.boxShadow = 'var(--shadow-md)';
        }

        lastScroll = currentScroll;
    });

    // Add to Cart Animation
    const cartIcon = document.querySelector('#cart-icon');
    const cartCount = document.querySelector('.cart-count');
    let count = parseInt(cartCount?.textContent || '0');



    // Notification helper function
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? 'var(--success-green)' : 'var(--error-red)'};
        color: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        z-index: 10000;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease;
    `;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Trigger animation
        requestAnimationFrame(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        });

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Product Card Hover Effect
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            card.style.setProperty('--x', `${x}px`);
            card.style.setProperty('--y', `${y}px`);
        });
    });

    function handleCardClick(productId, event) {

        if (event.target.classList.contains('buy-button') || event.target.closest('.buy-button')) {

            return;
        }


        window.location.href = `display.php?id=${productId}`;
    }

    async function addToCart(productId, event) {

        event.stopPropagation();

        try {
            // Ensure we have the button element
            const button = event?.target || event?.currentTarget;
            if (!button || !cartIcon) {
                console.error('Required elements not found');
                return;
            }

            // Send request to add item to cart
            const response = await fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });

            // Handle the response
            let result;
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                result = await response.json();
            } else {
                const text = await response.text();
                result = {
                    success: response.ok,
                    message: text
                };
            }

            if (result.success) {
                // Create flying animation
                const animationContainer = document.getElementById('cart-animation');
                if (!animationContainer) {
                    console.error('Animation container not found');
                    return;
                }

                const rect = button.getBoundingClientRect();
                const cartRect = cartIcon.getBoundingClientRect();

                const flyingElement = document.createElement('div');
                flyingElement.style.cssText = `
                position: fixed;
                width: 20px;
                height: 20px;
                background: var(--primary-purple);
                border-radius: 50%;
                top: ${rect.top + rect.height/2}px;
                left: ${rect.left + rect.width/2}px;
                pointer-events: none;
                transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                z-index: 9999;
            `;

                animationContainer.appendChild(flyingElement);

                // Trigger animation
                requestAnimationFrame(() => {
                    flyingElement.style.transform =
                        `translate(${cartRect.left - rect.left}px, ${cartRect.top - rect.top}px) scale(0)`;
                    flyingElement.style.opacity = '0';
                });

                // Update cart count and clean up
                setTimeout(() => {
                    count++;
                    if (cartCount) cartCount.textContent = count;
                    flyingElement.remove();
                }, 800);

                showNotification('Product added to cart!', 'success');
            } else {
                showNotification(result.message || 'Failed to add product to cart', 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showNotification('Failed to add product to cart', 'error');
        }
    }
    </script>
</body>

</html>