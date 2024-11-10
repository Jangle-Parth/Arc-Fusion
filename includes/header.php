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

    /* Header Styles - Same as previous */
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
    </style>
</head>

<body>
    <header class="header">
        <div class="header-top">
            Free shipping on orders above ₹999 | Shop Now!
        </div>

        <nav class="top-nav">
            <a href="/" class="logo">
                <img src="logo.png" alt="ArcFusion Logo">

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
                    <a href="profile.php">
                        <i class="far fa-user fa-lg"></i>
                    </a>
                </button>
                <button class="icon-btn">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span class="cart-count">2</span>
                </button>
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </header>
</body>

</html>