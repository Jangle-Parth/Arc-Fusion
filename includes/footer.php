<?php
  session_start();
  define("APPURL","http://localhost/WebTech/Project-New/");
//   require "D:/xampp/htdocs/WebTech/Project-New/config/config.php";

//   $categories=$conn->query("SELECT * FROM categories ORDER BY name DESC");
//   $categories->execute();
//   $allcategories=$categories->fetchAll(PDO::FETCH_OBJ); 
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
</body>

</html>