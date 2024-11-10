<!-- day to day home
industrial
decoration
gifts
asscesoories
custom designs -->
<?php
require_once 'vendor/autoload.php';
require_once 'product-manager.php';

// Initialize session if needed for user state
session_start();

// Get the product ID from URL
$productId = $_GET['id'] ?? null;

// Initialize variables to prevent undefined variable errors
$dailyDrops = ['success' => false, 'data' => []];
$bestSellers = ['success' => false, 'data' => []];

try {
    $productManager = new ProductManager();
    
    if (!$productId) {
        // Instead of redirecting, set a flag to show all products
        $showAllProducts = true;
        
        // Fetch daily drops and best sellers
        $dailyDrops = $productManager->getDailyDrops();
        $bestSellers = $productManager->getBestSellers();
    } else {
        // Fetch specific product details
        $productDetails = $productManager->getProductDetails($productId);
        
        if (!$productDetails['success']) {
            // Set error message instead of redirecting
            $errorMessage = "Product not found";
            $showAllProducts = true;
            
            // Still fetch other products to display
            $dailyDrops = $productManager->getDailyDrops();
            $bestSellers = $productManager->getBestSellers();
        } else {
            $product = $productDetails['data'];
        }
    }
} catch (Exception $e) {
    // Handle any database or other errors
    $errorMessage = "An error occurred while fetching products";
    $showAllProducts = true;
}

// Rest of your HTML remains the same until the main content section
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            link.addEventListener('mouseleave', function() {
                const fill = this.querySelector('.fill');
                fill.style.height = '0%';
            });
        });
    });
    </script>
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

    <main>
        <section class="slider-section fade-in">
            <div class="slider-container">
                <div class="slide active">
                    <img src="assets/images/carousel_1.jpg" alt="Member Promotion">
                </div>
                <div class="slide">
                    <img src="assets/images/carousel_2.jpg" alt="Member Promotion">
                </div>
                <div class="slide">
                    <img src="assets/images/carousel_3.png" alt="Member Promotion">
                </div>
            </div>
            <button class="slide-arrow prev-arrow"><i class="fas fa-chevron-left"></i></button>
            <button class="slide-arrow next-arrow"><i class="fas fa-chevron-right"></i></button>
        </section>

        <?php if (isset($errorMessage)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($product)): ?>
        <section class="product-detail">
            <div class="product-container">
                <img src="<?php echo htmlspecialchars($product['images'][0] ?? ''); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="price">₹<?php echo number_format($product['price'], 2); ?></p>
                    <?php if (isset($product['ratings'])): ?>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <span><?php echo number_format($product['ratings'], 1); ?></span>
                    </div>
                    <?php endif; ?>
                    <button class="add-to-cart-btn"
                        onclick="handleAddToCart('<?php echo htmlspecialchars((string)$product['_id']); ?>')">
                        Add to Cart
                    </button>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($showAllProducts): ?>
        <!-- Drop of the Day Section -->
        <section class="drops-section">
            <h2 class="section-title">DROP OF THE DAY</h2>
            <div class="card-container">
                <?php if ($dailyDrops['success'] && !empty($dailyDrops['data'])): ?>
                <?php foreach($dailyDrops['data'] as $product): ?>
                <div class="product-frame" data-product-id="<?php echo htmlspecialchars((string)$product['_id']); ?>">
                    <!-- Make the entire product frame clickable -->
                    <a href="display.php?id=<?php echo htmlspecialchars((string)$product['_id']); ?>"
                        class="product-link">
                        <img src="<?php echo htmlspecialchars($product['images'][0] ?? ''); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                        <div class="label-container">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <?php if (isset($product['ratings'])): ?>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <span><?php echo number_format($product['ratings'], 1); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <button class="add-to-cart-btn"
                        onclick="handleAddToCart('<?php echo htmlspecialchars((string)$product['_id']); ?>')">
                        Add to Cart
                    </button>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="no-products">No drops available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Best Sellers Section -->
        <section class="best-sellers">
            <h2 class="best-sellers-title">BEST SELLERS</h2>
            <div class="card-container">
                <?php if ($bestSellers['success'] && !empty($bestSellers['data'])): ?>
                <?php foreach($bestSellers['data'] as $product): ?>
                <div class="product-frame" data-product-id="<?php echo htmlspecialchars((string)$product['_id']); ?>">
                    <!-- Make the entire product frame clickable -->
                    <a href="display.php?id=<?php echo htmlspecialchars((string)$product['_id']); ?>"
                        class="product-link">
                        <img src="<?php echo htmlspecialchars($product['images'][0] ?? ''); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                        <div class="label-container">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <?php if (isset($product['ratings'])): ?>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <span><?php echo number_format($product['ratings'], 1); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <button class="add-to-cart-btn"
                        onclick="handleAddToCart('<?php echo htmlspecialchars((string)$product['_id']); ?>')">
                        Add to Cart
                    </button>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="no-products">No bestsellers available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Best Sellers Section -->
        <section class="best-sellers">
            <h2 class="best-sellers-title">BEST SELLERS</h2>
            <div class="card-container">
                <?php if ($bestSellers['success'] && !empty($bestSellers['data'])): ?>
                <?php foreach($bestSellers['data'] as $product): ?>
                <div class="product-frame" data-product-id="<?php echo (string)$product['_id']; ?>">
                    <a href="display.php?id=<?php echo (string)$product['_id']; ?>" class="product-link">
                        <img src="<?php echo htmlspecialchars($product['images'][0] ?? ''); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                        <div class="label-container">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <?php if (isset($product['ratings'])): ?>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <span><?php echo number_format($product['ratings'], 1); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <button class="add-to-cart-btn" onclick="handleAddToCart('<?php echo (string)$product['_id']; ?>')">
                        Add to Cart
                    </button>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="no-products">No bestsellers available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories-section">
            <h2 class="categories-title">CATEGORIES</h2>
            <div class="categories-grid-top">
                <div class="product-frame">
                    <a href="products.html" target="_blank">
                        <img src="assets\images\categories\image_1.jpg" alt="Device Stand">
                        <div class="price">₹2999</div>
                        <div class="label-container">
                            <h3 class="product-name">Industrial</h3>
                        </div>
                    </a>
                </div>

                <div class="product-frame">
                    <a href="products.html" target="_blank">
                        <img src="assets\images\categories\image_2.jpg" alt="AirPods Case">
                        <div class="price">₹1999</div>
                        <div class="label-container">
                            <h3 class="product-name">Holders</h3>
                        </div>
                    </a>
                </div>

                <div class="product-frame">
                    <a href="products.html" target="_blank">
                        <img src="assets\images\categories\image_3.jpg" alt="Mushroom Lamp">
                        <div class="price">₹399</div>
                        <div class="label-container">
                            <h3 class="product-name">Home Decor</h3>
                        </div>
                    </a>
                </div>
            </div>
            <div class="categories-grid-bottom">
                <div class="product-frame">
                    <a href="products.html" target="_blank">
                        <img src="assets\images\categories\image_4.jpg" alt="Device Stand">
                        <div class="price">₹999</div>
                        <div class="label-container">
                            <h3 class="product-name">Toys</h3>
                        </div>
                    </a>
                </div>

                <div class="product-frame">
                    <a href="products.html" target="_blank">
                        <img src="assets\images\categories\image_5.jpg" alt="AirPods Case">
                        <div class="price">₹1999</div>
                        <div class="label-container">
                            <h3 class="product-name">Gifts</h3>
                        </div>
                    </a>
                </div>

                <div class="product-frame">
                    <a href="products.html" target="_blank">
                        <img src="assets\images\categories\image_6.jpg" alt="Mushroom Lamp">
                        <div class="price">₹99</div>
                        <div class="label-container">
                            <h3 class="product-name">Custom Design</h3>

                        </div>
                    </a>
                </div>
                <div class="product-frame">
                    <a href="products.html" target="_blank">
                        <img src="assets\images\categories\image_7.jpg" alt="Mushroom Lamp">
                        <div class="price">₹399</div>
                        <div class="label-container">
                            <h3 class="product-name">Decoration</h3>
                        </div>
                    </a>
                </div>
            </div>
        </section>
        <?php endif; ?>
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
</body>

</html>