<?php
require_once 'vendor/autoload.php';
session_start();

// Get product ID from URL parameter
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: /products');
    exit;
}

try {
    // MongoDB connection settings
    $mongoUsername = "arcfusionindia";
    $mongoPassword = "SKu3QYP2zJuhoQps";
    $clusterUrl = "arcfusion.0j40w.mongodb.net";
    $database = "ArcFusion";

    $connectionString = "mongodb+srv://{$mongoUsername}:{$mongoPassword}@{$clusterUrl}/{$database}?retryWrites=true&w=majority";
     
    $mongoClient = new MongoDB\Client($connectionString);
    $db = $mongoClient->$database;
    $productsCollection = $db->products;

    // Fetch the product
    $product = $productsCollection->findOne(
        ['_id' => new MongoDB\BSON\ObjectId($productId)],
        [
            'projection' => [
                'name' => 1,
                'description' => 1,
                'price' => 1,
                'price2' => 1,
                'category' => 1,
                'colors' => 1,
                'images' => 1,
                'ratings' => 1
            ]
        ]
    );

    if (!$product) {
        header('Location: /404');
        exit;
    }

    // Convert MongoDB document to array
    $product = [
        'id' => (string)$product['_id'],
        'name' => $product['name'],
        'description' => $product['description'] ?? '',
        'price' => $product['price'] ?? 0,
        'price2' => $product['price2'] ?? 0,
        'category' => $product['category'] ?? 'Uncategorized',
        'colors' => $product['colors'] ?? ['#000000'],
        'images' => $product['images'] ?? [],
        'ratings' => $product['ratings'] ?? 0
    ];

} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: /error');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - ArcFusion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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



    /* Color selection */
    .color-selector {
        margin-bottom: 2rem;
    }

    .color-title {
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .color-options {
        display: flex;
        gap: 1rem;
    }

    .color-option {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
        transition: transform 0.3s ease;
    }

    .color-option:hover {
        transform: scale(1.1);
    }

    .color-option.selected::after {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        border: 2px solid var(--primary-purple);
        border-radius: 50%;
    }



    /* Main Product Section */
    .product-container {
        max-width: 1400px;
        margin: 100px auto 0;
        padding: 2rem;
        display: grid;
        grid-template-columns: 60% 40%;
        gap: 2rem;
    }

    /* Product Gallery */
    .product-gallery {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .main-image {
        width: 100%;
        height: 600px;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1rem;
        position: relative;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .main-image:hover img {
        transform: scale(1.05);
    }

    .thumbnail-container {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        opacity: 0.6;
        transition: all 0.3s ease;
    }

    .thumbnail.active {
        opacity: 1;
        border: 2px solid var(--primary-purple);
    }

    .thumbnail:hover {
        opacity: 1;
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Product Info */
    .product-info {
        padding: 2rem;
    }

    .product-category {
        color: var(--primary-purple);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .product-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stars {
        color: #FFD700;
    }

    .review-count {
        color: var(--text-light);
        text-decoration: none;
    }

    .product-price {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .original-price {
        text-decoration: line-through;
        color: var(--text-light);
        font-size: 1.5rem;
    }

    .discount {
        background: var(--success-green);
        color: var(--white);
        padding: 0.3rem 0.8rem;
        border-radius: 8px;
        font-size: 1rem;
    }

    .size-selector {
        margin-bottom: 2rem;
    }

    .size-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .size-guide {
        color: var(--primary-purple);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .size-options {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .size-option {
        width: 50px;
        height: 50px;
        border: 2px solid var(--text-light);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .size-option:hover {
        border-color: var(--primary-purple);
        color: var(--primary-purple);
    }

    .size-option.selected {
        background: var(--primary-purple);
        color: var(--white);
        border-color: var(--primary-purple);
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: var(--light-purple);
        border-radius: 8px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .quantity-btn:hover {
        background: var(--primary-purple);
        color: var(--white);
    }

    .quantity-input {
        width: 60px;
        height: 40px;
        text-align: center;
        border: 2px solid var(--light-purple);
        border-radius: 8px;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .add-to-cart,
    .buy-now {
        flex: 1;
        padding: 1rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .add-to-cart {
        background: var(--light-purple);
        color: var(--primary-purple);
    }

    .buy-now {
        background: var(--gradient-purple);
        color: var(--white);
    }

    .add-to-cart:hover,
    .buy-now:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .wishlist-btn {
        width: 50px;
        height: 50px;
        border: 2px solid var(--light-purple);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.2rem;
        color: var(--text-light);
    }

    .wishlist-btn:hover {
        border-color: var(--error-red);
        color: var(--error-red);
    }

    .product-details {
        margin-top: 3rem;
    }

    .details-tabs {
        display: flex;
        gap: 2rem;
        border-bottom: 2px solid var(--light-purple);
        margin-bottom: 2rem;
    }

    .tab {
        padding: 1rem 0;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-light);
        position: relative;
    }

    .tab.active {
        color: var(--primary-purple);
    }

    .tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--primary-purple);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.5s ease;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .feature-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .feature-icon {
        width: 30px;
        height: 30px;
        background: var(--light-purple);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-purple);
    }

    /* Delivery Info */
    .delivery-info {
        background: var(--light-purple);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .pincode-checker {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .pincode-input {
        flex: 1;
        padding: 0.8rem 1rem;
        border: 2px solid transparent;
        border-radius: 8px;
        outline: none;
        transition: all 0.3s ease;
    }

    .pincode-input:focus {
        border-color: var(--primary-purple);
    }

    .check-btn {
        padding: 0.8rem 1.5rem;
        background: var(--primary-purple);
        color: var(--white);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .check-btn:hover {
        background: var(--dark-purple);
    }

    /* Similar Products */
    .similar-products {
        margin-top: 4rem;
        padding: 2rem;
    }

    .section-title {
        font-size: 1.8rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .products-slider {
        display: flex;
        gap: 2rem;
        overflow-x: auto;
        padding: 1rem;
        scrollbar-width: none;
    }

    .products-slider::-webkit-scrollbar {
        display: none;
    }

    .product-card {
        min-width: 250px;
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .product-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-content {
        padding: 1rem;
    }

    @media (max-width: 1024px) {
        .product-container {
            grid-template-columns: 1fr;
        }

        .product-gallery {
            position: relative;
            top: 0;
        }

        .main-image {
            height: 400px;
        }
    }

    @media (max-width: 768px) {
        .feature-list {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .size-options {
            flex-wrap: wrap;
        }
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: var(--success-green);
        color: var(--white);
        padding: 1rem 2rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: var(--shadow-lg);
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
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

    <main class="product-container">
        <div class="product-gallery">
            <div class="main-image">
                <img src="<?php echo htmlspecialchars($product['images'][0] ?? 'assets/images/default.jpg'); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?> - Main">
            </div>
            <div class="thumbnail-container">
                <?php foreach ($product['images'] as $index => $image): ?>
                <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Thumbnail <?php echo $index + 1; ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="product-info">
            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="product-rating">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= $product['ratings']): ?>
                    <i class="fas fa-star"></i>
                    <?php elseif ($i - 0.5 <= $product['ratings']): ?>
                    <i class="fas fa-star-half-alt"></i>
                    <?php else: ?>
                    <i class="far fa-star"></i>
                    <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <a href="#" class="review-count"><?php echo number_format($product['ratings'], 1); ?> (128 reviews)</a>
            </div>

            <div class="product-price">
                <span>₹<?php echo number_format($product['price2']); ?></span>
                <span class="original-price">₹<?php echo number_format($product['price']); ?></span>
                <span class="discount">
                    -<?php echo round((1 - $product['price2'] / $product['price']) * 100); ?>%
                </span>
            </div>

            <div class="color-selector">
                <div class="color-title">Select Color</div>
                <div class="color-options">
                    <?php foreach ($product['colors'] as $index => $color): ?>
                    <div class="color-option <?php echo $index === 0 ? 'selected' : ''; ?>"
                        style="background-color: <?php echo htmlspecialchars($color); ?>"
                        title="<?php echo ucfirst(htmlspecialchars($color)); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="quantity-selector">
                <button class="quantity-btn">-</button>
                <input type="number" class="quantity-input" value="1" min="1" max="10">
                <button class="quantity-btn">+</button>
            </div>

            <div class="action-buttons">
                <button class="add-to-cart" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                    <i class="fas fa-shopping-cart"></i>
                    Add to Cart
                </button>
                <button class="buy-now">
                    <i class="fas fa-bolt"></i>
                    Buy Now
                </button>
                <button class="wishlist-btn">
                    <i class="far fa-heart"></i>
                </button>
            </div>

            <div class="product-details">
                <div class="details-tabs">
                    <div class="tab active">Description</div>
                    <div class="tab">Features</div>
                    <div class="tab">Reviews</div>
                </div>

                <div class="tab-content active">
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-tshirt"></i>
                            </div>
                            <span>Premium Cotton Blend</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-wind"></i>
                            </div>
                            <span>Breathable Fabric</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>Anti-Wrinkle</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <span>UV Protection</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="delivery-info">
                <h3>Check Delivery</h3>
                <div class="pincode-checker">
                    <input type="text" class="pincode-input" placeholder="Enter Pincode">
                    <button class="check-btn">Check</button>
                </div>
                <p>Free delivery on orders above ₹999</p>
            </div>
        </div>
    </main>

    <div class="toast">
        <i class="fas fa-check-circle"></i>
        <span>Added to cart successfully!</span>
    </div>

    <footer class="footer-container">
        <div class="brand-banner">
            <h2>HOMEGROWN INDIAN BRAND</h2>
        </div>

        <div class="footer-content">
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

            <div class="footer-column">
                <h3>MORE INFO</h3>
                <ul>
                    <li><a href="#">T&C</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Sitemap</a></li>
                    <li><a href="#">Blogs</a></li>
                </ul>
            </div>

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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Color selector functionality
        const colorOptions = document.querySelectorAll('.color-option');
        colorOptions.forEach(option => {
            option.addEventListener('click', function() {
                colorOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // Quantity selector functionality
        const quantityInput = document.querySelector('.quantity-input');
        const quantityBtns = document.querySelectorAll('.quantity-btn');
        quantityBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (this.textContent === '+' && currentValue < 10) {
                    quantityInput.value = currentValue + 1;
                } else if (this.textContent === '-' && currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
        });

        // Image gallery functionality
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.querySelector('.main-image img');
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                mainImage.src = this.querySelector('img').src;
            });
        });

        // Add to cart functionality
        const addToCartBtn = document.querySelector('.add-to-cart');
        const toast = document.querySelector('.toast');
        addToCartBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = quantityInput.value;
            const selectedColor = document.querySelector('.color-option.selected').getAttribute(
                'title');

            fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity,
                        color: selectedColor
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toast.classList.add('visible');
                        setTimeout(() => toast.classList.remove('visible'), 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
    </script>
</body>

</html>