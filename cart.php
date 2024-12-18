<?php
// cart.php - The main cart page
require_once 'vendor/autoload.php';
require 'cart-management.php';


// Initialize cart manager
$cartManager = new CartManager();
$userId = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;

// Get cart data
$cartData = $cartManager->getCart($userId);
$cartItems = $cartData['success'] ? $cartData['items'] : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
    :root {
        --primary: #7B6CF6;
        --secondary: #4A3F9F;
        --accent: #9D8DF7;
        --background: #F3F1FF;
        --text-dark: #2D2A45;
        --text-light: #6E6B85;
        --white: #FFFFFF;
        --shadow-sm: 0 2px 4px rgba(123, 108, 246, 0.1);
        --shadow-md: 0 4px 6px rgba(123, 108, 246, 0.15);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: var(--background);
        color: var(--text-dark);
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .cart-title {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .cart-count {
        background: var(--primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.9rem;
    }

    .cart-grid {
        display: grid;
        grid-template-columns: 7fr 3fr;
        gap: 2rem;
    }

    .cart-items {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
    }

    .cart-item {
        display: grid;
        grid-template-columns: auto 3fr 1fr 1fr auto;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .item-image {
        width: 100px;
        height: 100px;
        border-radius: 0.5rem;
        object-fit: cover;
    }

    .item-details h3 {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .item-details p {
        color: var(--text-light);
        font-size: 0.9rem;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quantity-btn {
        background: var(--background);
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .quantity-btn:hover {
        background: var(--primary);
        color: white;
    }

    .quantity {
        width: 40px;
        text-align: center;
        font-weight: 600;
    }

    .price {
        font-weight: 600;
        color: var(--primary);
    }

    .remove-btn {
        background: none;
        border: none;
        color: #ff4444;
        cursor: pointer;
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .remove-btn:hover {
        transform: scale(1.1);
    }

    .cart-summary {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        height: fit-content;
        box-shadow: var(--shadow-sm);
    }

    .summary-title {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        color: var(--text-light);
    }

    .total {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid rgba(0, 0, 0, 0.1);
        font-weight: 700;
        font-size: 1.2rem;
    }

    .checkout-btn {
        background: var(--primary);
        color: white;
        border: none;
        width: 100%;
        padding: 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        margin-top: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .checkout-btn:hover {
        background: var(--secondary);
        transform: translateY(-2px);
    }

    .empty-cart {
        text-align: center;
        padding: 3rem;
        color: var(--text-light);
    }

    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 2rem;
        background: var(--primary);
        color: white;
        border-radius: 0.5rem;
        box-shadow: var(--shadow-md);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1000;
    }

    .toast.show {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .cart-grid {
            grid-template-columns: 1fr;
        }

        .cart-item {
            grid-template-columns: auto 1fr;
            grid-template-rows: auto auto auto;
            gap: 1rem;
        }

        .item-image {
            width: 80px;
            height: 80px;
            grid-row: 1/3;
        }

        .quantity-controls {
            grid-column: 2;
        }

        .price {
            grid-column: 2;
        }

        .remove-btn {
            grid-column: 2;
            justify-self: end;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="cart-header">
            <h1 class="cart-title">Shopping Cart</h1>
            <span class="cart-count"><?php echo count($cartItems); ?> items</span>
        </div>

        <div class="cart-grid">
            <div class="cart-items" id="cartItems">
                <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <p>Add some items to your cart to see them here!</p>
                </div>
                <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo !empty($item['images']) ? htmlspecialchars($item['images'][0]) : '/images/placeholder.jpg'; ?>"
                        alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn"
                            onclick="updateQuantity('<?php echo $item['id']; ?>', <?php echo $item['quantity'] - 1; ?>)">-</button>
                        <span class="quantity"><?php echo $item['quantity']; ?></span>
                        <button class="quantity-btn"
                            onclick="updateQuantity('<?php echo $item['id']; ?>', <?php echo $item['quantity'] + 1; ?>)">+</button>
                    </div>
                    <span class="price">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    <button class="remove-btn" onclick="removeItem('<?php echo $item['id']; ?>')">×</button>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="cart-summary">
                <h2 class="summary-title">Order Summary</h2>
                <?php
                    $subtotal = array_reduce($cartItems, function($sum, $item) {
                        return $sum + ($item['price'] * $item['quantity']);
                    }, 0);
                    $shipping = $subtotal > 999 ? 0 : ($subtotal > 0 ? 99 : 0);
                    $tax = $subtotal * 0.18;
                    $total = $subtotal + $shipping + $tax;
                ?>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span id="subtotal">₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span id="shipping">₹<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax</span>
                    <span id="tax">₹<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="total">
                    <span>Total</span>
                    <span id="total">₹<?php echo number_format($total, 2); ?></span>
                </div>
                <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>


    <script>
    // Cart Management
    class CartAPI {
        static async getCart() {
            try {
                const response = await fetch('/cart-api.php');
                return await response.json();
            } catch (error) {
                showToast('Error loading cart');
                return {
                    success: false,
                    items: []
                };
            }
        }

        static async addToCart(productId, quantity = 1) {
            try {
                const response = await fetch('/cart-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity
                    }),
                });
                return await response.json();
            } catch (error) {
                showToast('Error adding item to cart');
                return {
                    success: false
                };
            }
        }

        static async updateQuantity(productId, quantity) {
            try {
                const response = await fetch('/cart-api.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity
                    }),
                });
                return await response.json();
            } catch (error) {
                showToast('Error updating quantity');
                return {
                    success: false
                };
            }
        }

        static async removeFromCart(productId) {
            try {
                const response = await fetch('/cart-api.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId
                    }),
                });
                return await response.json();
            } catch (error) {
                showToast('Error removing item');
                return {
                    success: false
                };
            }
        }
    }

    // UI Management
    let cartItems = [];

    async function loadCart() {
        const result = await CartAPI.getCart();
        if (result.success) {
            cartItems = result.items;
            updateCartDisplay();
        }
    }

    function updateCartDisplay() {
        const cartContainer = document.getElementById('cartItems');
        document.querySelector('.cart-count').textContent = `${cartItems.length} items`;

        if (cartItems.length === 0) {
            cartContainer.innerHTML = `
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some items to your cart to see them here!</p>
            </div>
        `;
            updateSummary();
            return;
        }

        cartContainer.innerHTML = cartItems.map(item => `
        <div class="cart-item">
            <img src="${item.images && item.images.length > 0 ? item.images[0] : '/images/placeholder.jpg'}" 
                alt="${item.name}" class="item-image">
            <div class="item-details">
                <h3>${item.name}</h3>
                <p>${item.description || ''}</p>
            </div>
            <div class="quantity-controls">
                <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.quantity - 1})">-</button>
                <span class="quantity">${item.quantity}</span>
                <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.quantity + 1})">+</button>
            </div>
            <span class="price">₹${(item.price * item.quantity).toFixed(2)}</span>
            <button class="remove-btn" onclick="removeItem('${item.id}')">×</button>
        </div>
    `).join('');

        updateSummary();
    }

    cartContainer.innerHTML = cartItems.map(item => `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}" class="item-image">
                    <div class="item-details">
                        <h3>${item.name}</h3>
                        <p>Quantity: ${item.quantity}</p>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.quantity - 1})">-</button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.quantity + 1})">+</button>
                    </div>
                    <span class="price">₹${(item.price * item.quantity).toFixed(2)}</span>
                    <button class="remove-btn" onclick="removeItem('${item.id}')">×</button>
                </div>
            `).join('');

    updateSummary();
    }

    function updateSummary() {
        const subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const shipping = subtotal > 999 ? 0 : subtotal > 0 ? 99 : 0;
        const tax = subtotal * 0.18; // 18% GST

        document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
        document.getElementById('shipping').textContent = `₹${shipping.toFixed(2)}`;
        document.getElementById('tax').textContent = `₹${tax.toFixed(2)}`;
        document.getElementById('total').textContent = `₹${(subtotal + shipping + tax).toFixed(2)}`;
    }

    async function updateQuantity(productId, newQuantity) {
        if (newQuantity < 1) {
            await removeItem(productId);
            return;
        }

        const result = await CartAPI.updateQuantity(productId, newQuantity);
        if (result.success) {
            await loadCart();
            showToast('Cart updated');
        }
    }

    async function removeItem(productId) {
        const result = await CartAPI.removeFromCart(productId);
        if (result.success) {
            await loadCart();
            showToast('Item removed from cart');
        }
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    function proceedToCheckout() {
        if (cartItems.length === 0) {
            showToast('Add items to cart first');
            return;
        }
        window.location.href = '/checkout.html';
    }

    // Initialize cart on page load
    document.addEventListener('DOMContentLoaded', loadCart);

    // Add this to your products.php page
    function addToCart(productId) {
        CartAPI.addToCart(productId)
            .then(result => {
                if (result.success) {
                    showToast('Item added to cart');
                    loadCart();
                }
            });
    }
    </script>
</body>

</html>