<?php
require_once 'php/db.php';

// Get database instance
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
    $productData = [
        'name' => isset($product['name']) ? $product['name'] : 'Untitled Product',
        'description' => isset($product['description']) ? $product['description'] : 'No description available',
        'price' => isset($product['price']) ? $product['price'] : '0',
        'price2' => isset($product['price2']) ? $product['price2'] : '',
        'images' => isset($product['images']) ? $product['images'] : [],
        '_id' => isset($product['_id']) ? (string)$product['_id'] : ''
    ];

    $productsHTML .= '
    <div class="card" data-product-id="' . $productData['_id'] . '">
        <img src="' . (isset($productData['images'][0]) ? htmlspecialchars($productData['images'][0]) : 'placeholder.jpg') . '" alt="' . htmlspecialchars($productData['name']) . '">
        <div class="card-content">
            <h3>' . htmlspecialchars($productData['name']) . '</h3>
            <p class="price">₹' . htmlspecialchars($productData['price']) . '</p>
            <button onclick="addToCart(\'' . $productData['_id'] . '\')" class="buy-button">Add to Cart</button>
        </div>
    </div>';
}
?>

<script>
async function addToCart(productId) {
    if (!isUserLoggedIn()) {
        window.location.href = 'login.html';
        return;
    }

    try {
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

        const result = await response.json();

        if (result.success) {
            updateCartCount();
            showNotification('Product added to cart!');
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        showNotification('Failed to add product to cart', 'error');
    }
}

function isUserLoggedIn() {
    return <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
}

async function updateCartCount() {
    try {
        const response = await fetch('cart-api.php');
        const result = await response.json();

        if (result.success) {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = result.items.length;
            }
        }
    } catch (error) {
        console.error('Failed to update cart count:', error);
    }
}

function showNotification(message, type = 'success') {
    // You can implement your own notification system here
    alert(message);
}

// Update cart count on page load
document.addEventListener('DOMContentLoaded', updateCartCount);
</script>